<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichFileType;

class MediaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Media')
            ->setEntityLabelInPlural('Medias');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield TextField::new('path', 'Apercu')
            ->formatValue(function ($value, ?Media $media) {
                if (!$media?->getPath()) {
                    return '';
                }

                $src = '/uploads/media/'.$media->getPath();
                $escapedSrc = htmlspecialchars($src, ENT_QUOTES);

                if ($media->isVideo()) {
                    return sprintf('<video src="%s" style="width:90px;height:60px;object-fit:cover;" muted preload="metadata"></video>', $escapedSrc);
                }

                return sprintf('<img src="%s" style="width:90px;height:60px;object-fit:cover;" alt="">', $escapedSrc);
            })
            ->renderAsHtml()
            ->onlyOnIndex();

        yield TextField::new('imageFile', 'Fichier')
            ->setFormType(VichFileType::class)
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->setHelp('Images acceptees : jpg, png, gif, webp, avif. Videos acceptees : mp4.')
            ->onlyOnForms();

        yield TextField::new('alt', 'Alt')->hideOnIndex();
        yield DateTimeField::new('updatedAt', 'MAJ')->hideOnForm();
        yield AssociationField::new('gallery', 'Galerie')->hideOnForm();
        yield AssociationField::new('groupPage', 'Groupe')->hideOnForm();
    }
}
