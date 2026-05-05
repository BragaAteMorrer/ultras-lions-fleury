<?php

namespace App\Controller\Admin;

use App\Entity\CartageQrToken;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CartageQrTokenCrudController extends AbstractCrudController
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public static function getEntityFqcn(): string
    {
        return CartageQrToken::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('QR cartage')
            ->setEntityLabelInPlural('QR cartage')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, fn (Action $action) => $action->setLabel('Voir le QR'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('label', 'Nom');
        yield TextField::new('token', 'Jeton')
            ->setHelp('URL du QR code : /cartage/qr/{jeton}');
        yield TextField::new('path', 'Lien QR')
            ->onlyOnDetail()
            ->formatValue(fn ($value, ?CartageQrToken $token) => $token ? $this->getAbsoluteQrUrl($token) : '');
        yield FormField::addPanel('QR code')->onlyOnDetail();
        yield TextField::new('qrPreview', 'QR code')
            ->onlyOnDetail()
            ->formatValue(function ($value, ?CartageQrToken $token) {
                if (!$token) {
                    return '';
                }

                $url = $this->getAbsoluteQrUrl($token);
                $qrSrc = 'https://api.qrserver.com/v1/create-qr-code/?size=260x260&data='.rawurlencode($url);

                return sprintf(
                    '<div style="display:flex;flex-direction:column;gap:12px;align-items:flex-start"><img src="%s" alt="QR code cartage" width="260" height="260"><code style="white-space:normal;word-break:break-all">%s</code></div>',
                    htmlspecialchars($qrSrc, ENT_QUOTES),
                    htmlspecialchars($url, ENT_QUOTES)
                );
            })
            ->renderAsHtml();
        yield MoneyField::new('amount', 'Prix')
            ->setCurrency('EUR')
            ->setStoredAsCents(false);
        yield BooleanField::new('enabled', 'Actif');
        yield IntegerField::new('usedCount', 'Utilisations')->hideOnForm();
        yield IntegerField::new('maxUses', 'Limite utilisations')->setRequired(false);
        yield DateTimeField::new('expiresAt', 'Expire le')->setRequired(false);
        yield DateTimeField::new('createdAt', 'Cree le')->hideOnForm();
    }

    private function getAbsoluteQrUrl(CartageQrToken $token): string
    {
        return $this->urlGenerator->generate(
            'cartage_qr_scan',
            ['token' => $token->getToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
