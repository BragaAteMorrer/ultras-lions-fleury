<?php

namespace App\Controller\Admin;

use App\Entity\MerchOrder;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;

class MerchOrderCrudController extends AbstractCrudController
{
    public function __construct(private MailService $mailService)
    {
    }

    public static function getEntityFqcn(): string
    {
        return MerchOrder::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande merch')
            ->setEntityLabelInPlural('Commandes merch')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $csv = Action::new('exportCsv', 'Exporter CSV')
            ->linkToCrudAction('exportCsv')
            ->setIcon('fa fa-file-csv')
            ->addCssClass('btn btn-secondary')
            ->createAsBatchAction();

        $pdf = Action::new('exportPdf', 'Exporter PDF')
            ->linkToCrudAction('exportPdf')
            ->setIcon('fa fa-file-pdf')
            ->addCssClass('btn btn-secondary')
            ->createAsBatchAction();

        return $actions
            ->add(Crud::PAGE_INDEX, $csv)
            ->add(Crud::PAGE_INDEX, $pdf);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('user', 'Acheteur')->formatValue(function ($value, $entity) {
            if (!$entity instanceof MerchOrder) {
                return '';
            }
            $user = $entity->getUser();
            if ($user) {
                $name = trim(($user->getPrenom() ?? '') . ' ' . ($user->getNom() ?? ''));
                return $name !== '' ? $name : $user->getUserIdentifier();
            }
            $name = trim(($entity->getCustomerFirstName() ?? '') . ' ' . ($entity->getCustomerLastName() ?? ''));
            if ($name !== '') {
                return $name;
            }
            return (string) ($entity->getEmail() ?? 'Invite');
        })->onlyOnIndex();
        yield AssociationField::new('user', 'Utilisateur')->onlyOnForms();
        yield AssociationField::new('merch', 'Produit');
        yield TextField::new('customerFirstName', 'Prenom')->onlyOnForms();
        yield TextField::new('customerLastName', 'Nom')->onlyOnForms();
        yield TextField::new('email', 'Email')->onlyOnForms();
        yield TextField::new('size', 'Taille')->hideOnForm();
        yield NumberField::new('quantity', 'Quantite');
        yield NumberField::new('unitPrice', 'Prix unitaire')->hideOnForm();
        yield NumberField::new('totalPrice', 'Total');
        yield ChoiceField::new('paymentMethod', 'Paiement')->setChoices([
            'SumUp' => 'sumup',
            'Liquide' => 'cash',
            'Gratuit' => 'free',
        ]);
        yield DateTimeField::new('createdAt', 'Date');
        yield BooleanField::new('executed', 'Commande executee');
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof MerchOrder) {
            parent::updateEntity($entityManager, $entityInstance);
            return;
        }

        $uow = $entityManager->getUnitOfWork();
        $uow->computeChangeSets();
        $changes = $uow->getEntityChangeSet($entityInstance);
        $executedChanged = array_key_exists('executed', $changes)
            && $changes['executed'][0] === false
            && $changes['executed'][1] === true;

        parent::updateEntity($entityManager, $entityInstance);

        if ($executedChanged) {
            $to = $entityInstance->getEmail();
            if (!$to && $entityInstance->getUser()) {
                $to = $entityInstance->getUser()->getUserIdentifier();
            }
            if ($to) {
                $this->mailService->send(
                    to: $to,
                    subject: 'Commande merch prete',
                    template: 'email/merch_order_ready.html.twig',
                    context: [
                        'order' => $entityInstance,
                    ]
                );
            }
        }
    }

    public function exportCsv(BatchActionDto $batchActionDto, EntityManagerInterface $em): Response
    {
        $ids = $batchActionDto->getEntityIds();
        $orders = $ids ? $em->getRepository(MerchOrder::class)->findBy(['id' => $ids]) : [];

        $lines = [];
        $lines[] = ['Date', 'Prenom', 'Nom', 'Produit', 'Taille', 'Quantite', 'Total', 'Paiement'];

        foreach ($orders as $order) {
            $user = $order->getUser();
            $first = $order->getCustomerFirstName() ?: ($user?->getPrenom() ?? '');
            $last = $order->getCustomerLastName() ?: ($user?->getNom() ?? '');
            $lines[] = [
                $order->getCreatedAt()?->format('d/m/Y H:i') ?? '',
                $first,
                $last,
                $order->getMerch()?->getTitle() ?? '',
                $order->getSize() ?? 'TU',
                (string) $order->getQuantity(),
                number_format($order->getTotalPrice(), 2, ',', ' '),
                $order->getPaymentMethod(),
            ];
        }

        $out = '';
        foreach ($lines as $line) {
            $escaped = array_map(static function ($v) {
                $v = (string) $v;
                $v = str_replace('"', '""', $v);
                return '"' . $v . '"';
            }, $line);
            $out .= implode(';', $escaped) . "\r\n";
        }

        return new Response($out, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="merch_orders.csv"',
        ]);
    }

    public function exportPdf(BatchActionDto $batchActionDto, EntityManagerInterface $em): Response
    {
        if (!class_exists(\Dompdf\Dompdf::class)) {
            throw new \RuntimeException('Installe dompdf/dompdf pour exporter en PDF.');
        }

        $ids = $batchActionDto->getEntityIds();
        $orders = $ids ? $em->getRepository(MerchOrder::class)->findBy(['id' => $ids]) : [];

        $totalAmount = 0.0;
        $totalQuantity = 0;
        foreach ($orders as $order) {
            $totalAmount += $order->getTotalPrice();
            $totalQuantity += $order->getQuantity();
        }

        $html = $this->pdfHeader('COMMANDES MERCH', count($orders), 'Articles', $totalQuantity, $totalAmount);
        $html .= '<table class="ultra-table">';
        $html .= '<thead><tr><th>Date</th><th>Acheteur</th><th>Produit</th><th class="center">Taille</th><th class="center">Qte</th><th class="right">Total</th><th>Paiement</th></tr></thead><tbody>';

        foreach ($orders as $order) {
            $user = $order->getUser();
            $first = $order->getCustomerFirstName() ?: ($user?->getPrenom() ?? '');
            $last = $order->getCustomerLastName() ?: ($user?->getNom() ?? '');
            $buyer = trim($first . ' ' . $last);
            $buyer = $buyer !== '' ? $buyer : ($order->getEmail() ?? 'Invite');

            $html .= '<tr>';
            $html .= '<td>' . ($order->getCreatedAt()?->format('d/m/Y H:i') ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($buyer) . '</td>';
            $html .= '<td>' . htmlspecialchars($order->getMerch()?->getTitle() ?? '') . '</td>';
            $html .= '<td class="center">' . htmlspecialchars($order->getSize() ?? 'TU') . '</td>';
            $html .= '<td class="center">' . $order->getQuantity() . '</td>';
            $html .= '<td class="right strong">' . number_format($order->getTotalPrice(), 2, ',', ' ') . ' EUR</td>';
            $html .= '<td><span class="badge badge-dark">' . htmlspecialchars($this->paymentLabel($order->getPaymentMethod())) . '</span></td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= $this->pdfFooter();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="merch_orders.pdf"',
        ]);
        /*

        $html = '<h2>Recapitulatif commandes merch</h2>';
        $html .= '<table width="100%" cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse;">';
        $html .= '<thead><tr><th>Date</th><th>Prenom</th><th>Nom</th><th>Produit</th><th>Taille</th><th>Quantite</th><th>Total</th><th>Paiement</th></tr></thead><tbody>';
        foreach ($orders as $order) {
            $user = $order->getUser();
            $first = $order->getCustomerFirstName() ?: ($user?->getPrenom() ?? '');
            $last = $order->getCustomerLastName() ?: ($user?->getNom() ?? '');
            $html .= '<tr>';
            $html .= '<td>' . ($order->getCreatedAt()?->format('d/m/Y H:i') ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($first) . '</td>';
            $html .= '<td>' . htmlspecialchars($last) . '</td>';
            $html .= '<td>' . htmlspecialchars($order->getMerch()?->getTitle() ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($order->getSize() ?? 'TU') . '</td>';
            $html .= '<td>' . $order->getQuantity() . '</td>';
            $html .= '<td>' . number_format($order->getTotalPrice(), 2, ',', ' ') . '</td>';
            $html .= '<td>' . htmlspecialchars($order->getPaymentMethod()) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="merch_orders.pdf"',
        ]);
        */
    }

    private function paymentLabel(string $method): string
    {
        return match ($method) {
            'cash' => 'Liquide',
            'free' => 'Gratuit',
            'sumup' => 'SumUp',
            default => ucfirst($method),
        };
    }

    private function pdfHeader(string $title, int $ordersCount, string $itemsLabel, int $itemsCount, float $totalAmount): string
    {
        return '<html><head><meta charset="UTF-8"><style>' . $this->pdfStyles() . '</style></head><body>'
            . '<div class="topbar"></div>'
            . '<div class="header">'
            . '<div class="brand">ULTRAS <span>LIONS</span></div>'
            . '<div class="subtitle">FC Fleury 91 - Export admin</div>'
            . '<h1>' . htmlspecialchars($title) . '</h1>'
            . '<div class="date">Genere le ' . (new \DateTimeImmutable())->format('d/m/Y H:i') . '</div>'
            . '</div>'
            . '<table class="stats"><tr>'
            . '<td><span>Commandes</span><strong>' . $ordersCount . '</strong></td>'
            . '<td><span>' . htmlspecialchars($itemsLabel) . '</span><strong>' . $itemsCount . '</strong></td>'
            . '<td><span>Total</span><strong>' . number_format($totalAmount, 2, ',', ' ') . ' EUR</strong></td>'
            . '</tr></table>';
    }

    private function pdfFooter(): string
    {
        return '<div class="footer">Ultras Lions Fleury - Document interne</div></body></html>';
    }

    private function pdfStyles(): string
    {
        return '
            @page { margin: 28px; }
            body { font-family: DejaVu Sans, Arial, sans-serif; color: #151515; font-size: 10px; }
            .topbar { height: 7px; background: #c40016; margin-bottom: 16px; }
            .header { background: #111; color: #fff; padding: 18px 20px; border-bottom: 4px solid #c40016; }
            .brand { font-size: 18px; font-weight: 800; letter-spacing: 1.5px; }
            .brand span { color: #c40016; }
            .subtitle { color: #bbb; margin-top: 3px; text-transform: uppercase; font-size: 8px; letter-spacing: .8px; }
            h1 { margin: 16px 0 4px; font-size: 23px; letter-spacing: .5px; }
            .date { color: #ddd; font-size: 9px; }
            .stats { width: 100%; border-collapse: collapse; margin: 16px 0 18px; }
            .stats td { background: #f3f3f3; border-left: 5px solid #c40016; padding: 10px 12px; width: 33%; }
            .stats span { display: block; color: #666; text-transform: uppercase; font-size: 8px; }
            .stats strong { display: block; margin-top: 4px; font-size: 16px; color: #111; }
            .ultra-table { width: 100%; border-collapse: collapse; }
            .ultra-table th { background: #111; color: #fff; padding: 8px 7px; text-transform: uppercase; font-size: 8px; border-bottom: 3px solid #c40016; }
            .ultra-table td { padding: 8px 7px; border-bottom: 1px solid #ddd; vertical-align: top; }
            .ultra-table tr:nth-child(even) td { background: #f7f7f7; }
            .center { text-align: center; }
            .right { text-align: right; }
            .strong { font-weight: 700; }
            .badge { display: inline-block; padding: 3px 7px; border-radius: 2px; font-size: 8px; font-weight: 700; text-transform: uppercase; }
            .badge-dark { background: #222; color: #fff; }
            .footer { position: fixed; bottom: -12px; left: 0; right: 0; color: #777; font-size: 8px; text-align: center; border-top: 1px solid #ddd; padding-top: 6px; }
        ';
    }
}
