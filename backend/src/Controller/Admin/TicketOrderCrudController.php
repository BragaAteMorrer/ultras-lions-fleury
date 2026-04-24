<?php

namespace App\Controller\Admin;

use App\Entity\Ticket;
use App\Entity\TicketOrder;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class TicketOrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TicketOrder::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande billet')
            ->setEntityLabelInPlural('Commandes billets')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $archive = Action::new('archiveSelected', 'Archiver')
            ->linkToCrudAction('archiveSelected')
            ->setIcon('fa fa-archive')
            ->addCssClass('btn btn-warning')
            ->createAsBatchAction();

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
            ->add(Crud::PAGE_INDEX, $archive)
            ->add(Crud::PAGE_INDEX, $csv)
            ->add(Crud::PAGE_INDEX, $pdf);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('ticket', 'Match'))
            ->add(DateTimeFilter::new('createdAt', 'Date'))
            ->add(DateTimeFilter::new('archivedAt', 'Archive le'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield AssociationField::new('user', 'Acheteur')->formatValue(function ($value, $entity) {
            if (!$entity instanceof TicketOrder) {
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

        yield TextField::new('ticket', 'Match')->formatValue(function ($value, $entity) {
            if (!$entity instanceof TicketOrder) {
                return '';
            }
            $ticket = $entity->getTicket();
            if (!$ticket instanceof Ticket) {
                return '';
            }
            $isAway = $ticket->getMatchLocation() ? (strtolower((string) $ticket->getMatchLocation()) === 'exterieur') : false;
            if (!$isAway && $ticket->getCategory()) {
                $isAway = $ticket->getCategory()->getSlug() === 'ext';
            }
            $label = $isAway
                ? sprintf('%s vs FC Fleury', (string) $ticket->getOpponent())
                : sprintf('FC Fleury vs %s', (string) $ticket->getOpponent());
            $where = $isAway ? 'Exterieur' : 'Domicile';
            return $label . ' (' . $where . ')';
        })->onlyOnIndex();
        yield AssociationField::new('ticket', 'Match')->onlyOnForms();

        yield TextField::new('customerFirstName', 'Prenom')->onlyOnForms();
        yield TextField::new('customerLastName', 'Nom')->onlyOnForms();
        yield TextField::new('email', 'Email')->onlyOnForms();
        yield NumberField::new('quantity', 'Quantite');
        yield NumberField::new('unitPrice', 'Prix unitaire')->hideOnForm();
        yield NumberField::new('totalPrice', 'Total');
        yield ChoiceField::new('paymentMethod', 'Paiement')->setChoices([
            'SumUp' => 'sumup',
            'Liquide' => 'cash',
            'Gratuit' => 'free',
        ]);
        yield BooleanField::new('paid', 'Paiement effectue');
        yield DateTimeField::new('createdAt', 'Date');
        yield DateTimeField::new('archivedAt', 'Archive le')->hideOnForm();
    }

    public function archiveSelected(
        BatchActionDto $batchActionDto,
        EntityManagerInterface $em,
        AdminUrlGenerator $adminUrlGenerator
    ): RedirectResponse {
        $ids = $batchActionDto->getEntityIds();
        foreach ($ids as $id) {
            $order = $em->getRepository(TicketOrder::class)->find($id);
            if ($order instanceof TicketOrder) {
                $order->setArchivedAt(new \DateTime());
            }
        }
        $em->flush();

        $this->addFlash('success', 'Commandes archivees.');

        $url = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(Crud::PAGE_INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function exportCsv(BatchActionDto $batchActionDto, EntityManagerInterface $em): Response
    {
        $ids = $batchActionDto->getEntityIds();
        $orders = $ids ? $em->getRepository(TicketOrder::class)->findBy(['id' => $ids]) : [];

        $lines = [];
        $lines[] = ['Date', 'Prenom', 'Nom', 'Match', 'Quantite', 'Total', 'Paiement', 'Paye'];

        foreach ($orders as $order) {
            $user = $order->getUser();
            $first = $order->getCustomerFirstName() ?: ($user?->getPrenom() ?? '');
            $last = $order->getCustomerLastName() ?: ($user?->getNom() ?? '');
            $ticket = $order->getTicket();
            $match = $ticket ? ($ticket->getOpponent() ?? $ticket->getTitle()) : '';
            $lines[] = [
                $order->getCreatedAt()?->format('d/m/Y H:i') ?? '',
                $first,
                $last,
                $match,
                (string) $order->getQuantity(),
                number_format($order->getTotalPrice(), 2, ',', ' '),
                $order->getPaymentMethod(),
                $order->isPaid() ? 'oui' : 'non',
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
            'Content-Disposition' => 'attachment; filename=\"ticket_orders.csv\"',
        ]);
    }

    public function exportPdf(BatchActionDto $batchActionDto, EntityManagerInterface $em): Response
    {
        if (!class_exists(\Dompdf\Dompdf::class)) {
            throw new \RuntimeException('Installe dompdf/dompdf pour exporter en PDF.');
        }

        $ids = $batchActionDto->getEntityIds();
        $orders = $ids ? $em->getRepository(TicketOrder::class)->findBy(['id' => $ids]) : [];

        $html = '<h2>Recapitulatif commandes billets</h2>';
        $html .= '<table width=\"100%\" cellpadding=\"6\" cellspacing=\"0\" border=\"1\" style=\"border-collapse:collapse;\">';
        $html .= '<thead><tr><th>Date</th><th>Prenom</th><th>Nom</th><th>Match</th><th>Quantite</th><th>Total</th><th>Paiement</th><th>Paye</th></tr></thead><tbody>';
        foreach ($orders as $order) {
            $user = $order->getUser();
            $first = $order->getCustomerFirstName() ?: ($user?->getPrenom() ?? '');
            $last = $order->getCustomerLastName() ?: ($user?->getNom() ?? '');
            $ticket = $order->getTicket();
            $match = $ticket ? ($ticket->getOpponent() ?? $ticket->getTitle()) : '';
            $html .= '<tr>';
            $html .= '<td>' . ($order->getCreatedAt()?->format('d/m/Y H:i') ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($first) . '</td>';
            $html .= '<td>' . htmlspecialchars($last) . '</td>';
            $html .= '<td>' . htmlspecialchars($match) . '</td>';
            $html .= '<td>' . $order->getQuantity() . '</td>';
            $html .= '<td>' . number_format($order->getTotalPrice(), 2, ',', ' ') . '</td>';
            $html .= '<td>' . htmlspecialchars($order->getPaymentMethod()) . '</td>';
            $html .= '<td>' . ($order->isPaid() ? 'oui' : 'non') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename=\"ticket_orders.pdf\"',
        ]);
    }
}
