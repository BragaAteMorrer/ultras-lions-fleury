<?php

namespace App\Service;

use App\Entity\EventCategory;
use App\Entity\Event;
use App\Entity\Gadget;
use App\Entity\Chant;
use App\Entity\Donation;
use App\Entity\LinktreeLink;
use App\Entity\CartageQrToken;
use App\Entity\CartageRegistration;
use App\Entity\Gallery;
use App\Entity\GroupPage;
use App\Entity\InviteCode;
use App\Entity\Media;
use App\Entity\Merch;
use App\Entity\MerchCategory;
use App\Entity\MerchOrder;
use App\Entity\MerchStock;
use App\Form\MerchStockType;
use App\Entity\Page;
use App\Entity\PaymentCheckout;
use App\Entity\Post;
use App\Entity\SiteConfig;
use App\Entity\Ticket;
use App\Entity\TicketCategory;
use App\Entity\TicketOrder;
use App\Entity\User;
use App\Entity\BilletwebLead;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdminCrudRegistry
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function all(): array
    {
        return [
            'articles' => [
                'label' => 'Articles',
                'singular' => 'Article',
                'entity' => Post::class,
                'order' => ['createdAt' => 'DESC', 'id' => 'DESC'],
                'list' => [
                    'id' => 'ID',
                    'title' => 'Titre',
                    'category' => 'Categorie',
                    'createdAt' => 'Cree le',
                    'updatedAt' => 'Mis a jour',
                ],
                'fields' => [
                    'title' => ['type' => TextType::class, 'label' => 'Titre', 'attr' => ['data-page-title' => '1']],
                    'slug' => ['type' => TextType::class, 'label' => 'Slug', 'required' => false, 'help' => 'Laisse vide pour generer automatiquement.', 'attr' => ['data-page-slug' => '1']],
                    'category' => [
                        'type' => EntityType::class,
                        'label' => 'Categorie',
                        'required' => false,
                        'class' => EventCategory::class,
                        'choice_label' => 'name',
                        'placeholder' => 'Aucune categorie',
                        'query_builder' => static fn ($repository) => $repository
                            ->createQueryBuilder('category')
                            ->andWhere('category.section = :section')
                            ->setParameter('section', 'articles')
                            ->orderBy('category.name', 'ASC'),
                        'attr' => [
                            'data-page-category' => '1',
                        ],
                    ],
                    'image' => [
                        'type' => EntityType::class,
                        'label' => 'Image principale',
                        'required' => false,
                        'class' => Media::class,
                        'choice_label' => static fn (?Media $media): string => $media instanceof Media
                            ? (trim((string) $media->getAlt()) !== ''
                                ? trim((string) $media->getAlt())
                                : (trim((string) $media->getPath()) !== '' ? trim((string) $media->getPath()) : sprintf('Media #%d', $media->getId() ?? 0)))
                            : '',
                        'choice_attr' => static fn (?Media $media): array => $media instanceof Media && $media->getPath()
                            ? [
                                'data-media-path' => $media->getPath(),
                                'data-media-alt' => $media->getAlt() ?? '',
                                'data-media-kind' => $media->isVideo() ? 'video' : 'image',
                            ]
                            : [],
                        'placeholder' => 'Aucune image',
                    ],
                    'content' => ['type' => TextareaType::class, 'label' => 'Contenu', 'required' => false, 'attr' => ['rows' => 22, 'data-page-content' => '1']],
                ],
            ],
            'categories-contenu' => [
                'label' => 'Categories contenu',
                'singular' => 'Categorie contenu',
                'entity' => EventCategory::class,
                'order' => ['name' => 'ASC'],
                'list' => [
                    'id' => 'ID',
                    'name' => 'Nom',
                    'slug' => 'Slug',
                    'section' => 'Section',
                ],
                'fields' => [
                    'name' => ['type' => TextType::class, 'label' => 'Nom'],
                    'slug' => ['type' => TextType::class, 'label' => 'Slug', 'required' => false, 'help' => 'Laisse vide pour generer automatiquement.'],
                    'section' => [
                        'type' => ChoiceType::class,
                        'label' => 'Section',
                        'choices' => [
                            'Articles' => 'articles',
                            'Photos de match' => 'photos_de_match',
                            'Evenements' => 'evenements',
                            'Medias' => 'medias',
                        ],
                    ],
                ],
            ],
            'pages' => [
                'label' => 'Pages',
                'singular' => 'Page',
                'entity' => Page::class,
                'order' => ['createdAt' => 'DESC', 'id' => 'DESC'],
                'list' => [
                    'id' => 'ID',
                    'title' => 'Titre',
                    'slug' => 'Slug',
                    'createdAt' => 'Cree le',
                ],
                'fields' => [
                    'title' => [
                        'type' => TextType::class,
                        'label' => 'Titre',
                        'attr' => [
                            'placeholder' => 'Exemple : Règlement intérieur',
                            'maxlength' => 180,
                            'data-page-title' => '1',
                        ],
                        'constraints' => [
                            new Assert\NotBlank(message: 'Le titre est obligatoire.'),
                            new Assert\Length(max: 180),
                        ],
                    ],
                    'slug' => [
                        'type' => TextType::class,
                        'label' => 'Slug',
                        'required' => false,
                        'help' => 'Le slug correspond à l’adresse de la page. Exemple : reglement-interieur',
                        'attr' => [
                            'placeholder' => 'reglement-interieur',
                            'data-page-slug' => '1',
                        ],
                    ],
                    'image' => [
                        'type' => EntityType::class,
                        'label' => 'Image principale',
                        'required' => false,
                        'class' => Media::class,
                        'choice_label' => static fn (?Media $media): string => $media instanceof Media
                            ? (trim((string) $media->getAlt()) !== ''
                                ? trim((string) $media->getAlt())
                                : (trim((string) $media->getPath()) !== '' ? trim((string) $media->getPath()) : sprintf('Media #%d', $media->getId() ?? 0)))
                            : '',
                        'choice_attr' => static fn (?Media $media): array => $media instanceof Media && $media->getPath()
                            ? [
                                'data-media-path' => $media->getPath(),
                                'data-media-alt' => $media->getAlt() ?? '',
                                'data-media-kind' => $media->isVideo() ? 'video' : 'image',
                            ]
                            : [],
                        'placeholder' => 'Aucune image principale',
                        'help' => 'Choisis une image existante ou ouvre la médiathèque pour en ajouter une autre.',
                    ],
                    'content' => [
                        'type' => TextareaType::class,
                        'label' => 'Contenu',
                        'required' => true,
                        'help' => 'L’éditeur visuel nettoie automatiquement le HTML dangereux.',
                        'constraints' => [
                            new Assert\NotBlank(message: 'Le contenu de la page ne peut pas être vide.'),
                        ],
                        'attr' => [
                            'rows' => 22,
                            'data-page-content' => '1',
                        ],
                    ],
                ],
            ],
            'chants' => [
                'label' => 'Chants',
                'singular' => 'Chant',
                'entity' => Chant::class,
                'order' => ['title' => 'ASC'],
                'list' => [
                    'id' => 'ID',
                    'title' => 'Titre',
                    'audioPath' => 'Audio',
                    'updatedAt' => 'Mis a jour',
                ],
                'fields' => [
                    'title' => ['type' => TextType::class, 'label' => 'Titre', 'attr' => ['data-chant-title' => '1']],
                    'audioFile' => [
                        'type' => FileType::class,
                        'label' => 'Fichier audio',
                        'required' => false,
                        'help' => 'Importe un fichier MP3, WAV, OGG, M4A, AAC ou FLAC.',
                        'attr' => [
                            'accept' => 'audio/*,.mp3,.wav,.ogg,.m4a,.aac,.flac',
                            'data-chant-audio-file' => '1',
                        ],
                        'constraints' => [
                            new Assert\File([
                                'mimeTypes' => [
                                    'audio/mpeg',
                                    'audio/mp3',
                                    'audio/wav',
                                    'audio/x-wav',
                                    'audio/ogg',
                                    'audio/mp4',
                                    'audio/aac',
                                    'audio/x-aac',
                                    'audio/flac',
                                    'audio/x-flac',
                                ],
                                'mimeTypesMessage' => 'Le fichier importe doit etre un audio valide.',
                            ]),
                        ],
                    ],
                    'lyrics' => ['type' => TextareaType::class, 'label' => 'Paroles', 'required' => false, 'attr' => ['rows' => 18, 'data-chant-lyrics' => '1']],
                ],
            ],
            'linktree' => [
                'label' => 'Linktree',
                'singular' => 'Lien Linktree',
                'entity' => LinktreeLink::class,
                'order' => ['position' => 'ASC', 'id' => 'ASC'],
                'list' => [
                    'id' => 'ID',
                    'position' => 'Ordre',
                    'title' => 'Titre',
                    'url' => 'URL',
                    'enabled' => 'Actif',
                    'featured' => 'Principal',
                ],
                'fields' => [
                    'position' => ['type' => IntegerType::class, 'label' => 'Ordre', 'attr' => ['data-linktree-position' => '1']],
                    'title' => ['type' => TextType::class, 'label' => 'Titre', 'attr' => ['data-linktree-title' => '1']],
                    'description' => ['type' => TextType::class, 'label' => 'Sous-titre', 'required' => false, 'attr' => ['data-linktree-description' => '1']],
                    'url' => ['type' => TextType::class, 'label' => 'URL', 'help' => 'Exemples : /billetterie/ ou https://www.instagram.com/ultraslionsfleury91/', 'attr' => ['data-linktree-url' => '1']],
                    'icon' => ['type' => TextType::class, 'label' => 'Icone Bootstrap', 'required' => false, 'help' => 'Exemples : ticket-perforated, bag, camera-reels, instagram.', 'attr' => ['data-linktree-icon' => '1']],
                    'enabled' => ['type' => CheckboxType::class, 'label' => 'Actif', 'required' => false],
                    'featured' => ['type' => CheckboxType::class, 'label' => 'Lien principal', 'required' => false],
                    'openInNewTab' => ['type' => CheckboxType::class, 'label' => 'Nouvel onglet', 'required' => false],
                ],
            ],
            'gadgets' => [
                'label' => 'Gadgets du groupe',
                'singular' => 'Gadget',
                'entity' => Gadget::class,
                'order' => ['position' => 'ASC', 'createdAt' => 'DESC'],
                'list' => [
                    'id' => 'ID',
                    'position' => 'Ordre',
                    'title' => 'Nom',
                    'productionLabel' => 'Serie / annee',
                    'visible' => 'Visible',
                ],
                'fields' => [
                    'position' => ['type' => IntegerType::class, 'label' => 'Ordre'],
                    'title' => ['type' => TextType::class, 'label' => 'Nom'],
                    'productionLabel' => ['type' => TextType::class, 'label' => 'Serie / annee', 'required' => false],
                    'image' => [
                        'type' => EntityType::class,
                        'label' => 'Photo',
                        'required' => false,
                        'class' => Media::class,
                        'choice_label' => 'path',
                        'placeholder' => 'Aucune photo',
                    ],
                    'visible' => ['type' => CheckboxType::class, 'label' => 'Visible', 'required' => false],
                    'description' => ['type' => TextareaType::class, 'label' => 'Description', 'required' => false, 'attr' => ['rows' => 10]],
                ],
            ],
            'categories-merch' => [
                'label' => 'Categories merch',
                'singular' => 'Categorie merch',
                'entity' => MerchCategory::class,
                'order' => ['name' => 'ASC'],
                'list' => [
                    'id' => 'ID',
                    'name' => 'Nom',
                    'slug' => 'Slug',
                ],
                'fields' => [
                    'name' => ['type' => TextType::class, 'label' => 'Nom'],
                    'slug' => ['type' => TextType::class, 'label' => 'Slug', 'required' => false, 'help' => 'Laisse vide pour generer automatiquement.'],
                ],
            ],
            'categories-billetterie' => [
                'label' => 'Categories billetterie',
                'singular' => 'Categorie billetterie',
                'entity' => TicketCategory::class,
                'order' => ['name' => 'ASC'],
                'list' => [
                    'id' => 'ID',
                    'name' => 'Nom',
                    'slug' => 'Slug',
                ],
                'fields' => [
                    'name' => ['type' => TextType::class, 'label' => 'Nom'],
                    'slug' => ['type' => TextType::class, 'label' => 'Slug', 'required' => false, 'help' => 'Laisse vide pour generer automatiquement.'],
                ],
            ],
            'merch' => [
                'label' => 'Merch',
                'singular' => 'Produit',
                'entity' => Merch::class,
                'order' => ['id' => 'DESC'],
                'list' => ['id' => 'ID', 'title' => 'Titre', 'category' => 'Categorie', 'price' => 'Prix', 'memberPrice' => 'Prix membre', 'audience' => 'Visibilite', 'publicUntil' => 'Visible jusqu\'au', 'displayInGadgetHistory' => 'Gadget'],
                'fields' => [
                    'title' => ['type' => TextType::class, 'label' => 'Titre', 'attr' => ['data-merch-title' => '1']],
                    'category' => ['type' => EntityType::class, 'label' => 'Categorie', 'required' => false, 'class' => MerchCategory::class, 'choice_label' => 'name', 'placeholder' => 'Aucune categorie', 'attr' => ['data-merch-category' => '1']],
                    'price' => ['type' => MoneyType::class, 'label' => 'Prix non-connecte', 'currency' => 'EUR', 'attr' => ['data-merch-price' => '1']],
                    'memberPrice' => ['type' => MoneyType::class, 'label' => 'Prix connecte', 'currency' => 'EUR', 'required' => false, 'attr' => ['data-merch-member-price' => '1']],
                    'audience' => ['type' => ChoiceType::class, 'label' => 'Visibilite', 'choices' => ['Tous les visiteurs' => 'public', 'Membres uniquement' => 'members'], 'help' => 'Tous les visiteurs = membres et non-membres. Membres uniquement = produit reserve aux comptes connectes.', 'attr' => ['data-merch-audience' => '1']],
                    'publicUntil' => ['type' => DateTimeType::class, 'label' => 'Visible jusqu\'au', 'required' => false, 'widget' => 'single_text', 'input' => 'datetime_immutable', 'help' => 'A partir de cette date, le produit n\'apparait plus au public.'],
                    'image' => ['type' => EntityType::class, 'label' => 'Image principale', 'required' => false, 'class' => Media::class, 'choice_label' => static fn (?Media $media): string => $media instanceof Media
                        ? (trim((string) $media->getAlt()) !== ''
                            ? trim((string) $media->getAlt())
                            : (trim((string) $media->getPath()) !== '' ? trim((string) $media->getPath()) : sprintf('Media #%d', $media->getId() ?? 0)))
                        : '', 'choice_attr' => static fn (?Media $media): array => $media instanceof Media && $media->getPath()
                        ? [
                            'data-media-path' => $media->getPath(),
                            'data-media-alt' => $media->getAlt() ?? '',
                            'data-media-kind' => $media->isVideo() ? 'video' : 'image',
                        ]
                        : [], 'placeholder' => 'Aucune image', 'attr' => ['data-merch-image' => '1']],
                    'displayInGadgetHistory' => ['type' => CheckboxType::class, 'label' => 'Afficher dans Gadgets du groupe', 'required' => false, 'attr' => ['data-merch-gadget' => '1']],
                    'stocks' => [
                        'type' => CollectionType::class,
                        'label' => 'Tailles et stocks',
                        'entry_type' => MerchStockType::class,
                        'entry_options' => [
                            'category_slug' => null,
                        ],
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                        'required' => false,
                        'prototype' => true,
                        'prototype_name' => '__size__',
                    ],
                    'description' => ['type' => TextareaType::class, 'label' => 'Description', 'required' => false, 'attr' => ['rows' => 12, 'data-merch-description' => '1']],
                ],
            ],
            'dons' => [
                'label' => 'Dons',
                'singular' => 'Don',
                'entity' => Donation::class,
                'order' => ['createdAt' => 'DESC', 'id' => 'DESC'],
                'list' => [
                    'id' => 'ID',
                    'createdAt' => 'Créé le',
                    'donorFirstName' => 'Prénom',
                    'donorLastName' => 'Nom',
                    'donorEmail' => 'Email',
                    'amount' => 'Montant',
                    'currency' => 'Devise',
                    'status' => 'Statut',
                    'paymentReference' => 'Référence',
                    'paymentStatus' => 'Paiement',
                ],
                'fields' => [
                    'createdAt' => ['type' => DateTimeType::class, 'label' => 'Créé le', 'widget' => 'single_text', 'input' => 'datetime_immutable'],
                    'donorFirstName' => ['type' => TextType::class, 'label' => 'Prénom', 'required' => false],
                    'donorLastName' => ['type' => TextType::class, 'label' => 'Nom', 'required' => false],
                    'donorEmail' => ['type' => EmailType::class, 'label' => 'Email', 'required' => false],
                    'amount' => ['type' => MoneyType::class, 'label' => 'Montant', 'currency' => 'EUR'],
                    'currency' => ['type' => TextType::class, 'label' => 'Devise'],
                    'status' => ['type' => ChoiceType::class, 'label' => 'Statut', 'choices' => [
                        'En attente' => Donation::STATUS_PENDING,
                        'Payé' => Donation::STATUS_PAID,
                        'Échoué' => Donation::STATUS_FAILED,
                        'Annulé' => Donation::STATUS_CANCELLED,
                        'Remboursé' => Donation::STATUS_REFUNDED,
                    ]],
                    'paymentReference' => ['type' => TextType::class, 'label' => 'Référence', 'required' => false],
                    'paymentStatus' => ['type' => TextType::class, 'label' => 'Paiement', 'required' => false],
                    'anonymous' => ['type' => CheckboxType::class, 'label' => 'Anonyme', 'required' => false],
                    'message' => ['type' => TextareaType::class, 'label' => 'Message', 'required' => false, 'attr' => ['rows' => 5]],
                ],
            ],
            'stocks-merch' => [
                'label' => 'Stocks merch',
                'singular' => 'Stock merch',
                'entity' => MerchStock::class,
                'order' => ['id' => 'DESC'],
                'list' => ['id' => 'ID', 'merch' => 'Produit', 'size' => 'Taille', 'sumupSku' => 'SKU SumUp', 'quantity' => 'Quantite'],
                'fields' => [
                    'merch' => ['type' => EntityType::class, 'label' => 'Produit', 'class' => Merch::class, 'choice_label' => 'title'],
                    'size' => ['type' => TextType::class, 'label' => 'Taille'],
                    'sumupSku' => ['type' => TextType::class, 'label' => 'SKU SumUp', 'required' => false],
                    'quantity' => ['type' => IntegerType::class, 'label' => 'Quantite'],
                ],
            ],
            'commandes-merch' => [
                'label' => 'Commandes merch',
                'singular' => 'Commande merch',
                'entity' => MerchOrder::class,
                'order' => ['createdAt' => 'DESC'],
                'list' => ['id' => 'ID', 'createdAt' => 'Date', 'email' => 'Email', 'merch' => 'Produit', 'size' => 'Taille', 'quantity' => 'Qte', 'totalPrice' => 'Total', 'paymentMethod' => 'Paiement', 'sumupReference' => 'Ref SumUp', 'executed' => 'Executee'],
                'fields' => [
                    'user' => ['type' => EntityType::class, 'label' => 'Utilisateur', 'required' => false, 'class' => User::class, 'choice_label' => 'email', 'placeholder' => 'Invite'],
                    'merch' => ['type' => EntityType::class, 'label' => 'Produit', 'class' => Merch::class, 'choice_label' => 'title'],
                    'email' => ['type' => EmailType::class, 'label' => 'Email', 'required' => false],
                    'customerFirstName' => ['type' => TextType::class, 'label' => 'Prenom', 'required' => false],
                    'customerLastName' => ['type' => TextType::class, 'label' => 'Nom', 'required' => false],
                    'size' => ['type' => TextType::class, 'label' => 'Taille', 'required' => false],
                    'quantity' => ['type' => IntegerType::class, 'label' => 'Quantite'],
                    'unitPrice' => ['type' => MoneyType::class, 'label' => 'Prix unitaire', 'currency' => 'EUR'],
                    'totalPrice' => ['type' => MoneyType::class, 'label' => 'Total', 'currency' => 'EUR'],
                    'paymentMethod' => ['type' => ChoiceType::class, 'label' => 'Paiement', 'choices' => ['SumUp en ligne' => 'sumup', 'SumUp physique' => 'sumup_pos', 'SumUp import' => 'sumup_import', 'Liquide' => 'cash', 'Gratuit' => 'free']],
                    'sumupReference' => ['type' => TextType::class, 'label' => 'Reference SumUp', 'required' => false],
                    'executed' => ['type' => CheckboxType::class, 'label' => 'Commande executee', 'required' => false],
                    'note' => ['type' => TextareaType::class, 'label' => 'Note', 'required' => false, 'attr' => ['rows' => 6]],
                ],
            ],
            'billets' => [
                'label' => 'Billetterie',
                'singular' => 'Billet',
                'entity' => Ticket::class,
                'deletable' => false,
                'order' => ['matchDate' => 'DESC'],
                'list' => ['id' => 'ID', 'title' => 'Titre', 'opponent' => 'Adversaire', 'matchDate' => 'Date', 'category' => 'Categorie', 'price' => 'Prix', 'stock' => 'Stock', 'openToGuests' => 'Acces'],
                'fields' => [
                    'title' => ['type' => TextType::class, 'label' => 'Titre'],
                    'opponent' => ['type' => TextType::class, 'label' => 'Adversaire'],
                    'matchDate' => ['type' => DateTimeType::class, 'label' => 'Date du match', 'widget' => 'single_text'],
                    'guestAvailabilityDate' => ['type' => DateTimeType::class, 'label' => 'Ouverture invites', 'required' => false, 'widget' => 'single_text'],
                    'memberAvailabilityDate' => ['type' => DateTimeType::class, 'label' => 'Ouverture membres', 'required' => false, 'widget' => 'single_text'],
                    'openToGuests' => ['type' => CheckboxType::class, 'label' => 'Ouverte aux non-membres', 'required' => false, 'help' => 'Decoche pour reserver la vente aux membres connectes.'],
                    'matchLocation' => ['type' => ChoiceType::class, 'label' => 'Lieu', 'required' => false, 'choices' => ['Domicile' => 'domicile', 'Exterieur' => 'exterieur']],
                    'venue' => ['type' => TextType::class, 'label' => 'Stade / lieu', 'required' => false],
                    'billetwebUrl' => ['type' => TextType::class, 'label' => 'URL Billetweb', 'required' => false],
                    'category' => [
                        'type' => EntityType::class,
                        'label' => 'Categorie',
                        'required' => false,
                        'class' => TicketCategory::class,
                        'choice_label' => 'name',
                        'choice_attr' => static fn (?TicketCategory $category): array => $category instanceof TicketCategory
                            ? ['data-category-slug' => $category->getSlug() ?? '']
                            : [],
                        'placeholder' => 'Aucune categorie',
                    ],
                    'image' => [
                        'type' => EntityType::class,
                        'label' => 'Image',
                        'required' => false,
                        'class' => Media::class,
                        'choice_label' => static fn (?Media $media): string => $media instanceof Media
                            ? (trim((string) $media->getAlt()) !== ''
                                ? trim((string) $media->getAlt())
                                : (trim((string) $media->getPath()) !== '' ? trim((string) $media->getPath()) : sprintf('Media #%d', $media->getId() ?? 0)))
                            : '',
                        'choice_attr' => static fn (?Media $media): array => $media instanceof Media && $media->getPath()
                            ? [
                                'data-media-path' => $media->getPath(),
                                'data-media-alt' => $media->getAlt() ?? '',
                                'data-media-kind' => $media->isVideo() ? 'video' : 'image',
                            ]
                            : [],
                        'placeholder' => 'Aucune image',
                    ],
                    'price' => ['type' => MoneyType::class, 'label' => 'Prix', 'currency' => 'EUR'],
                    'stock' => ['type' => IntegerType::class, 'label' => 'Stock'],
                    'description' => ['type' => TextareaType::class, 'label' => 'Description', 'required' => false, 'attr' => ['rows' => 10]],
                ],
            ],
            'commandes-billets' => [
                'label' => 'Commandes billets',
                'singular' => 'Commande billet',
                'entity' => TicketOrder::class,
                'deletable' => false,
                'order' => ['createdAt' => 'DESC'],
                'list' => ['id' => 'ID', 'createdAt' => 'Date', 'email' => 'Email', 'ticket' => 'Match', 'quantity' => 'Qte', 'totalPrice' => 'Total', 'paymentMethod' => 'Paiement', 'paid' => 'Paye'],
                'fields' => [
                    'user' => ['type' => EntityType::class, 'label' => 'Utilisateur', 'required' => false, 'class' => User::class, 'choice_label' => 'email', 'placeholder' => 'Invite'],
                    'ticket' => ['type' => EntityType::class, 'label' => 'Match', 'class' => Ticket::class],
                    'email' => ['type' => EmailType::class, 'label' => 'Email', 'required' => false],
                    'customerFirstName' => ['type' => TextType::class, 'label' => 'Prenom', 'required' => false],
                    'customerLastName' => ['type' => TextType::class, 'label' => 'Nom', 'required' => false],
                    'quantity' => ['type' => IntegerType::class, 'label' => 'Quantite'],
                    'unitPrice' => ['type' => MoneyType::class, 'label' => 'Prix unitaire', 'currency' => 'EUR'],
                    'totalPrice' => ['type' => MoneyType::class, 'label' => 'Total', 'currency' => 'EUR'],
                    'paymentMethod' => ['type' => ChoiceType::class, 'label' => 'Paiement', 'choices' => ['SumUp' => 'sumup', 'Liquide' => 'cash', 'Gratuit' => 'free']],
                    'paid' => ['type' => CheckboxType::class, 'label' => 'Paiement effectue', 'required' => false],
                    'note' => ['type' => TextareaType::class, 'label' => 'Note', 'required' => false, 'attr' => ['rows' => 6]],
                ],
            ],
            'paiements' => [
                'label' => 'Paiements SumUp',
                'singular' => 'Paiement SumUp',
                'entity' => PaymentCheckout::class,
                'order' => ['createdAt' => 'DESC'],
                'list' => ['id' => 'ID', 'createdAt' => 'Cree le', 'type' => 'Type', 'status' => 'Statut', 'checkoutReference' => 'Reference', 'amount' => 'Montant', 'email' => 'Email'],
                'fields' => [
                    'type' => ['type' => ChoiceType::class, 'label' => 'Type', 'choices' => ['Merch' => 'merch', 'Billet' => 'ticket', 'Mixte' => 'mixed', 'Cartage' => 'cartage']],
                    'status' => ['type' => ChoiceType::class, 'label' => 'Statut', 'choices' => ['En attente' => 'pending', 'Paye' => 'paid', 'Echoue' => 'failed']],
                    'sumupCheckoutId' => ['type' => TextType::class, 'label' => 'Checkout ID', 'required' => false],
                    'checkoutReference' => ['type' => TextType::class, 'label' => 'Reference', 'required' => false],
                    'amount' => ['type' => MoneyType::class, 'label' => 'Montant', 'currency' => 'EUR'],
                    'currency' => ['type' => TextType::class, 'label' => 'Devise'],
                    'user' => ['type' => EntityType::class, 'label' => 'Utilisateur', 'required' => false, 'class' => User::class, 'choice_label' => 'email', 'placeholder' => 'Invite'],
                    'email' => ['type' => EmailType::class, 'label' => 'Email', 'required' => false],
                ],
            ],
            'evenements' => [
                'label' => 'Evenements',
                'singular' => 'Evenement',
                'entity' => Event::class,
                'order' => ['date' => 'DESC'],
                'list' => ['id' => 'ID', 'date' => 'Date', 'title' => 'Titre', 'category' => 'Categorie', 'season' => 'Saison'],
                'fields' => [
                    'title' => ['type' => TextType::class, 'label' => 'Titre'],
                    'date' => ['type' => DateTimeType::class, 'label' => 'Date', 'widget' => 'single_text'],
                    'category' => ['type' => EntityType::class, 'label' => 'Categorie', 'required' => false, 'class' => EventCategory::class, 'choice_label' => 'name', 'placeholder' => 'Aucune categorie'],
                    'opponent' => ['type' => TextType::class, 'label' => 'Adversaire', 'required' => false],
                    'matchLocation' => ['type' => ChoiceType::class, 'label' => 'Lieu match', 'required' => false, 'choices' => ['Domicile' => 'domicile', 'Exterieur' => 'exterieur']],
                    'season' => ['type' => TextType::class, 'label' => 'Saison', 'required' => false],
                    'journee' => ['type' => IntegerType::class, 'label' => 'Journee', 'required' => false],
                    'image' => [
                        'type' => EntityType::class,
                        'label' => 'Image principale',
                        'required' => false,
                        'class' => Media::class,
                        'choice_label' => static fn (?Media $media): string => $media instanceof Media
                            ? (trim((string) $media->getAlt()) !== ''
                                ? trim((string) $media->getAlt())
                                : (trim((string) $media->getPath()) !== '' ? trim((string) $media->getPath()) : sprintf('Media #%d', $media->getId() ?? 0)))
                            : '',
                        'choice_attr' => static fn (?Media $media): array => $media instanceof Media && $media->getPath()
                            ? [
                                'data-media-path' => $media->getPath(),
                                'data-media-alt' => $media->getAlt() ?? '',
                                'data-media-kind' => $media->isVideo() ? 'video' : 'image',
                            ]
                            : [],
                        'placeholder' => 'Aucune image',
                    ],
                    'media' => [
                        'type' => EntityType::class,
                        'label' => 'Galerie',
                        'required' => false,
                        'class' => Media::class,
                        'multiple' => true,
                        'by_reference' => false,
                        'choice_label' => static fn (?Media $media): string => $media instanceof Media
                            ? (trim((string) $media->getAlt()) !== ''
                                ? trim((string) $media->getAlt())
                                : (trim((string) $media->getPath()) !== '' ? trim((string) $media->getPath()) : sprintf('Media #%d', $media->getId() ?? 0)))
                            : '',
                        'choice_attr' => static fn (?Media $media): array => $media instanceof Media && $media->getPath()
                            ? [
                                'data-media-path' => $media->getPath(),
                                'data-media-alt' => $media->getAlt() ?? '',
                                'data-media-kind' => $media->isVideo() ? 'video' : 'image',
                            ]
                            : [],
                    ],
                    'description' => ['type' => TextareaType::class, 'label' => 'Description', 'required' => false, 'attr' => ['rows' => 12, 'data-event-description' => '1']],
                ],
            ],
            'galeries' => [
                'label' => 'Galeries',
                'singular' => 'Galerie',
                'entity' => Gallery::class,
                'order' => ['id' => 'DESC'],
                'list' => ['id' => 'ID', 'title' => 'Titre', 'description' => 'Description'],
                'fields' => [
                    'title' => ['type' => TextType::class, 'label' => 'Titre'],
                    'description' => ['type' => TextareaType::class, 'label' => 'Description', 'required' => false, 'attr' => ['rows' => 8]],
                    'media' => [
                        'type' => EntityType::class,
                        'label' => 'Medias de la galerie',
                        'required' => false,
                        'class' => Media::class,
                        'multiple' => true,
                        'by_reference' => false,
                        'choice_label' => static fn (?Media $media): string => $media instanceof Media
                            ? (trim((string) $media->getAlt()) !== ''
                                ? trim((string) $media->getAlt())
                                : (trim((string) $media->getPath()) !== '' ? trim((string) $media->getPath()) : sprintf('Media #%d', $media->getId() ?? 0)))
                            : '',
                        'choice_attr' => static fn (?Media $media): array => $media instanceof Media && $media->getPath()
                            ? [
                                'data-media-path' => $media->getPath(),
                                'data-media-alt' => $media->getAlt() ?? '',
                                'data-media-kind' => $media->isVideo() ? 'video' : 'image',
                            ]
                            : [],
                    ],
                ],
            ],
            'medias' => [
                'label' => 'Medias',
                'singular' => 'Media',
                'entity' => Media::class,
                'order' => ['id' => 'DESC'],
                'list' => ['id' => 'ID', 'path' => 'Fichier', 'alt' => 'Alt', 'gallery' => 'Galerie', 'event' => 'Evenement'],
                'fields' => [
                    'imageFile' => ['type' => FileType::class, 'label' => 'Fichier a importer', 'required' => false, 'help' => 'Choisis une image ou une video a televerser.', 'attr' => ['accept' => 'image/*,video/mp4,video/webm,video/quicktime']],
                    'path' => ['type' => TextType::class, 'label' => 'Nom du fichier', 'required' => false, 'attr' => ['data-media-path' => '1']],
                    'alt' => ['type' => TextType::class, 'label' => 'Texte alternatif', 'required' => false, 'attr' => ['data-media-alt' => '1']],
                    'gallery' => ['type' => EntityType::class, 'label' => 'Galerie', 'required' => false, 'class' => Gallery::class, 'choice_label' => 'title', 'placeholder' => 'Aucune galerie'],
                    'event' => ['type' => EntityType::class, 'label' => 'Evenement', 'required' => false, 'class' => Event::class, 'choice_label' => 'title', 'placeholder' => 'Aucun evenement'],
                    'post' => ['type' => EntityType::class, 'label' => 'Article', 'required' => false, 'class' => Post::class, 'choice_label' => 'title', 'placeholder' => 'Aucun article'],
                    'merch' => ['type' => EntityType::class, 'label' => 'Produit merch', 'required' => false, 'class' => Merch::class, 'choice_label' => 'title', 'placeholder' => 'Aucun produit'],
                    'groupPage' => ['type' => EntityType::class, 'label' => 'Page groupe', 'required' => false, 'class' => GroupPage::class, 'choice_label' => 'name', 'placeholder' => 'Aucune page'],
                ],
            ],
            'cartages' => [
                'label' => 'Cartages',
                'singular' => 'Cartage',
                'entity' => CartageRegistration::class,
                'order' => ['createdAt' => 'DESC'],
                'list' => ['id' => 'ID', 'createdAt' => 'Date', 'firstName' => 'Prenom', 'lastName' => 'Nom', 'email' => 'Email', 'season' => 'Saison', 'status' => 'Statut', 'paymentMethod' => 'Paiement'],
                'fields' => [
                    'firstName' => ['type' => TextType::class, 'label' => 'Prenom'],
                    'lastName' => ['type' => TextType::class, 'label' => 'Nom'],
                    'email' => ['type' => EmailType::class, 'label' => 'Email'],
                    'phone' => ['type' => TextType::class, 'label' => 'Telephone'],
                    'birthDate' => ['type' => DateType::class, 'label' => 'Date de naissance', 'required' => false, 'widget' => 'single_text', 'input' => 'datetime_immutable'],
                    'address' => ['type' => TextType::class, 'label' => 'Adresse', 'required' => false],
                    'postalCode' => ['type' => TextType::class, 'label' => 'Code postal', 'required' => false],
                    'city' => ['type' => TextType::class, 'label' => 'Ville', 'required' => false],
                    'amount' => ['type' => MoneyType::class, 'label' => 'Montant', 'currency' => 'EUR'],
                    'paymentMethod' => ['type' => ChoiceType::class, 'label' => 'Paiement', 'choices' => ['Liquide' => 'cash', 'En ligne' => 'online']],
                    'status' => ['type' => ChoiceType::class, 'label' => 'Statut', 'choices' => ['Attente liquide' => 'pending_cash', 'Attente en ligne' => 'pending_online', 'Paye en ligne' => 'paid_online', 'Valide liquide' => 'validated_cash', 'Annule' => 'cancelled']],
                    'season' => ['type' => TextType::class, 'label' => 'Saison'],
                    'checkoutReference' => ['type' => TextType::class, 'label' => 'Reference checkout', 'required' => false],
                    'sumupCheckoutId' => ['type' => TextType::class, 'label' => 'Checkout SumUp', 'required' => false],
                ],
            ],
            'qr-cartage' => [
                'label' => 'QR codes cartage',
                'singular' => 'QR cartage',
                'entity' => CartageQrToken::class,
                'order' => ['createdAt' => 'DESC'],
                'list' => ['id' => 'ID', 'label' => 'Libelle', 'amount' => 'Montant', 'enabled' => 'Actif', 'usedCount' => 'Utilisations', 'maxUses' => 'Max', 'expiresAt' => 'Expire le'],
                'fields' => [
                    'label' => ['type' => TextType::class, 'label' => 'Libelle'],
                    'token' => ['type' => TextType::class, 'label' => 'Jeton'],
                    'amount' => ['type' => MoneyType::class, 'label' => 'Montant', 'currency' => 'EUR'],
                    'enabled' => ['type' => CheckboxType::class, 'label' => 'Actif', 'required' => false],
                    'maxUses' => ['type' => IntegerType::class, 'label' => 'Nombre max', 'required' => false],
                    'usedCount' => ['type' => IntegerType::class, 'label' => 'Utilisations'],
                    'expiresAt' => ['type' => DateTimeType::class, 'label' => 'Expire le', 'required' => false, 'widget' => 'single_text', 'input' => 'datetime_immutable'],
                ],
            ],
            'utilisateurs' => [
                'label' => 'Utilisateurs',
                'singular' => 'Utilisateur',
                'entity' => User::class,
                'order' => ['email' => 'ASC'],
                'list' => ['id' => 'ID', 'email' => 'Email', 'prenom' => 'Prenom', 'nom' => 'Nom', 'telephone' => 'Telephone'],
                'fields' => [
                    'email' => ['type' => EmailType::class, 'label' => 'Email'],
                    'plainPassword' => ['type' => PasswordType::class, 'label' => 'Nouveau mot de passe', 'required' => false, 'mapped' => true],
                    'roles' => ['type' => ChoiceType::class, 'label' => 'Roles', 'multiple' => true, 'expanded' => true, 'choices' => ['Utilisateur' => 'ROLE_USER', 'Admin' => 'ROLE_ADMIN']],
                    'prenom' => ['type' => TextType::class, 'label' => 'Prenom', 'required' => false],
                    'nom' => ['type' => TextType::class, 'label' => 'Nom', 'required' => false],
                    'telephone' => ['type' => TextType::class, 'label' => 'Telephone', 'required' => false],
                    'adresse' => ['type' => TextType::class, 'label' => 'Adresse', 'required' => false],
                    'ville' => ['type' => TextType::class, 'label' => 'Ville', 'required' => false],
                    'codePostal' => ['type' => TextType::class, 'label' => 'Code postal', 'required' => false],
                    'dateNaissance' => ['type' => DateType::class, 'label' => 'Date de naissance', 'required' => false, 'widget' => 'single_text'],
                ],
            ],
            'codes-acces' => [
                'label' => 'Codes acces',
                'singular' => 'Code acces',
                'entity' => InviteCode::class,
                'order' => ['id' => 'DESC'],
                'list' => ['id' => 'ID', 'code' => 'Code', 'used' => 'Utilise', 'expiresAt' => 'Expire le'],
                'fields' => [
                    'code' => ['type' => TextType::class, 'label' => 'Code'],
                    'used' => ['type' => CheckboxType::class, 'label' => 'Utilise', 'required' => false],
                    'expiresAt' => ['type' => DateTimeType::class, 'label' => 'Expire le', 'widget' => 'single_text', 'input' => 'datetime_immutable'],
                ],
            ],
            'groupe' => [
                'label' => 'Le groupe',
                'singular' => 'Page groupe',
                'entity' => GroupPage::class,
                'order' => ['id' => 'ASC'],
                'list' => ['id' => 'ID', 'name' => 'Nom', 'description' => 'Description'],
                'fields' => [
                    'name' => [
                        'type' => TextType::class,
                        'label' => 'Nom',
                        'attr' => [
                            'placeholder' => 'Le groupe',
                            'data-group-name' => '1',
                            'maxlength' => 120,
                        ],
                        'constraints' => [
                            new Assert\NotBlank(message: 'Le nom du groupe est obligatoire.'),
                            new Assert\Length(max: 120),
                        ],
                    ],
                    'logo' => [
                        'type' => EntityType::class,
                        'label' => 'Logo',
                        'required' => false,
                        'class' => Media::class,
                        'choice_label' => static fn (?Media $media): string => $media instanceof Media
                            ? (trim((string) $media->getAlt()) !== ''
                                ? trim((string) $media->getAlt())
                                : (trim((string) $media->getPath()) !== '' ? trim((string) $media->getPath()) : sprintf('Media #%d', $media->getId() ?? 0)))
                            : '',
                        'choice_attr' => static fn (?Media $media): array => $media instanceof Media && $media->getPath()
                            ? [
                                'data-media-path' => $media->getPath(),
                                'data-media-alt' => $media->getAlt() ?? '',
                                'data-media-kind' => $media->isVideo() ? 'video' : 'image',
                            ]
                            : [],
                        'placeholder' => 'Aucun logo',
                    ],
                    'banner' => [
                        'type' => EntityType::class,
                        'label' => 'Banniere',
                        'required' => false,
                        'class' => Media::class,
                        'choice_label' => static fn (?Media $media): string => $media instanceof Media
                            ? (trim((string) $media->getAlt()) !== ''
                                ? trim((string) $media->getAlt())
                                : (trim((string) $media->getPath()) !== '' ? trim((string) $media->getPath()) : sprintf('Media #%d', $media->getId() ?? 0)))
                            : '',
                        'choice_attr' => static fn (?Media $media): array => $media instanceof Media && $media->getPath()
                            ? [
                                'data-media-path' => $media->getPath(),
                                'data-media-alt' => $media->getAlt() ?? '',
                                'data-media-kind' => $media->isVideo() ? 'video' : 'image',
                            ]
                            : [],
                        'placeholder' => 'Aucune banniere',
                    ],
                    'description' => [
                        'type' => TextareaType::class,
                        'label' => 'Description',
                        'required' => false,
                        'attr' => ['rows' => 7, 'data-group-rich' => '1'],
                    ],
                    'histoireText' => [
                        'type' => TextareaType::class,
                        'label' => 'Histoire',
                        'required' => false,
                        'attr' => ['rows' => 8, 'data-group-rich' => '1'],
                    ],
                    'mentaliteText' => [
                        'type' => TextareaType::class,
                        'label' => 'Mentalite',
                        'required' => false,
                        'attr' => ['rows' => 8, 'data-group-rich' => '1'],
                    ],
                    'fonctionnementText' => [
                        'type' => TextareaType::class,
                        'label' => 'Fonctionnement',
                        'required' => false,
                        'attr' => ['rows' => 8, 'data-group-rich' => '1'],
                    ],
                    'rejoindreText' => [
                        'type' => TextareaType::class,
                        'label' => 'Rejoindre',
                        'required' => false,
                        'attr' => ['rows' => 8, 'data-group-rich' => '1'],
                    ],
                    'seCarterText' => [
                        'type' => TextareaType::class,
                        'label' => 'Se carter',
                        'required' => false,
                        'attr' => ['rows' => 8, 'data-group-rich' => '1'],
                    ],
                ],
            ],
            'configuration' => [
                'label' => 'Configuration site',
                'singular' => 'Configuration',
                'entity' => SiteConfig::class,
                'order' => ['id' => 'ASC'],
                'list' => ['id' => 'ID', 'siteName' => 'Nom du site', 'heroTitle' => 'Titre hero', 'chantsTitle' => 'Titre chants'],
                'fields' => [
                    'siteName' => ['type' => TextType::class, 'label' => 'Nom du site', 'required' => false],
                    'heroTitle' => ['type' => TextType::class, 'label' => 'Titre hero', 'required' => false],
                    'heroSubtitle' => ['type' => TextareaType::class, 'label' => 'Sous-titre hero', 'required' => false, 'attr' => ['rows' => 5]],
                    'chantsTitle' => ['type' => TextType::class, 'label' => 'Titre chants', 'required' => false],
                    'chantsSubtitle' => ['type' => TextareaType::class, 'label' => 'Sous-titre chants', 'required' => false, 'attr' => ['rows' => 5]],
                    'backgroundImage' => ['type' => EntityType::class, 'label' => 'Image de fond', 'required' => false, 'class' => Media::class, 'choice_label' => 'path', 'placeholder' => 'Aucune image'],
                ],
            ],
            'preinscriptions-billetweb' => [
                'label' => 'Pre-inscriptions Billetweb',
                'singular' => 'Pre-inscription',
                'entity' => BilletwebLead::class,
                'deletable' => false,
                'order' => ['createdAt' => 'DESC'],
                'list' => ['id' => 'ID', 'createdAt' => 'Date', 'ticket' => 'Match', 'firstName' => 'Prenom', 'lastName' => 'Nom', 'email' => 'Email'],
                'fields' => [
                    'ticket' => ['type' => EntityType::class, 'label' => 'Match', 'class' => Ticket::class],
                    'user' => ['type' => EntityType::class, 'label' => 'Utilisateur', 'required' => false, 'class' => User::class, 'choice_label' => 'email', 'placeholder' => 'Invite'],
                    'firstName' => ['type' => TextType::class, 'label' => 'Prenom'],
                    'lastName' => ['type' => TextType::class, 'label' => 'Nom'],
                    'email' => ['type' => EmailType::class, 'label' => 'Email'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $resource): array
    {
        $resources = $this->all();

        if (!isset($resources[$resource])) {
            throw new \InvalidArgumentException(sprintf('Ressource admin inconnue: %s', $resource));
        }

        return $resources[$resource];
    }

    public function has(string $resource): bool
    {
        return isset($this->all()[$resource]);
    }
}
