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
            'Content-Disposition' => 'attachment; filename=\"merch_orders.csv\"',
        ]);
    }

    public function exportPdf(BatchActionDto $batchActionDto, EntityManagerInterface $em): Response
    {
        if (!class_exists(\Dompdf\Dompdf::class)) {
            throw new \RuntimeException('Installe dompdf/dompdf pour exporter en PDF.');
        }

        $ids = $batchActionDto->getEntityIds();
        $orders = $ids ? $em->getRepository(MerchOrder::class)->findBy(['id' => $ids]) : [];

        $html = '<h2>Recapitulatif commandes merch</h2>';
        $html .= '<table width=\"100%\" cellpadding=\"6\" cellspacing=\"0\" border=\"1\" style=\"border-collapse:collapse;\">';
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
            'Content-Disposition' => 'attachment; filename=\"merch_orders.pdf\"',
        ]);
    }
}
