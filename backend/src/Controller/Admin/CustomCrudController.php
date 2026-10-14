<?php

namespace App\Controller\Admin;

use App\Entity\BilletwebLead;
use App\Entity\CartageRegistration;
use App\Entity\EventCategory;
use App\Entity\Event;
use App\Entity\Chant;
use App\Entity\Donation;
use App\Entity\GroupPage;
use App\Entity\LinktreeLink;
use App\Entity\Media;
use App\Entity\Merch;
use App\Entity\MerchCategory;
use App\Entity\MerchOrder;
use App\Entity\Page;
use App\Entity\PaymentCheckout;
use App\Entity\Post;
use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\TicketCategory;
use App\Entity\TicketOrder;
use App\Repository\DonationRepository;
use App\Service\AdminCrudRegistry;
use App\Repository\MerchOrderRepository;
use App\Repository\MediaRepository;
use App\Repository\PageRepository;
use App\Service\MerchGadgetHistorySynchronizer;
use App\Service\AdminNavigationBuilder;
use App\Service\MailService;
use App\Service\PageContentSanitizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/manage')]
class CustomCrudController extends AbstractController
{
    public function __construct(
        private readonly AdminCrudRegistry $crudRegistry,
        private readonly AdminNavigationBuilder $adminNavigationBuilder,
        private readonly EntityManagerInterface $entityManager,
        private readonly FormFactoryInterface $formFactory,
        private readonly SluggerInterface $slugger,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MerchGadgetHistorySynchronizer $gadgetHistorySynchronizer,
        private readonly PageRepository $pageRepository,
        private readonly MediaRepository $mediaRepository,
        private readonly PageContentSanitizer $pageContentSanitizer,
        private readonly MailService $mailService,
    ) {
    }

    #[Route('/{resource}', name: 'custom_admin_crud_index', methods: ['GET'])]
    public function index(Request $request, string $resource): Response
    {
        $config = $this->crudRegistry->get($resource);
        $search = trim((string) $request->query->get('q', ''));
        $sort = trim((string) $request->query->get('sort', ''));
        $direction = strtolower(trim((string) $request->query->get('direction', 'asc'))) === 'desc' ? 'desc' : 'asc';
        $state = trim((string) $request->query->get('state', ''));
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = max(5, min(50, (int) $request->query->get('perPage', 20)));

        $filterDefinitions = $this->buildFilterDefinitions($config);
        $queryParams = $request->query->all();
        $repository = $this->entityManager->getRepository($config['entity']);

        // Use SQL pagination for unfiltered lists rather than hydrating every entity.
        // Keep the existing PHP filtering/sorting path for computed and related fields.
        $hasActiveFilters = $search !== '' || ($resource === 'commandes-merch' && $state !== '');
        foreach ($filterDefinitions as $definition) {
            $name = $definition['name'];
            $suffixes = match ($definition['type']) {
                'date_range' => ['_from', '_to'],
                'number_range' => ['_min', '_max'],
                default => [''],
            };
            foreach ($suffixes as $suffix) {
                if (trim((string) ($queryParams[$name.$suffix] ?? '')) !== '') {
                    $hasActiveFilters = true;
                    break 2;
                }
            }
        }

        $order = $config['order'] ?? ['id' => 'DESC'];
        $metadata = $this->entityManager->getClassMetadata($config['entity']);
        $sqlSort = $sort !== '' && array_key_exists($sort, $config['list']) && $metadata->hasField($sort);
        if (!$hasActiveFilters && ($sort === '' || $sqlSort)) {
            if ($sqlSort) {
                $order = [$sort => strtoupper($direction)];
                if ($sort !== 'id' && $metadata->hasField('id')) {
                    $order['id'] = 'DESC';
                }
            }
            $totalItems = $repository->count([]);
            $totalPages = max(1, (int) ceil($totalItems / $perPage));
            $page = min($page, $totalPages);
            $offset = ($page - 1) * $perPage;
            $pageItems = $repository->findBy([], $order, $perPage, $offset);
        } else {
            $items = $repository->findBy([], $order);
            $items = $this->applyFilters($items, $config, $search, $queryParams, $filterDefinitions);
            if ($resource === 'commandes-merch') {
                $items = $this->applyMerchOrderStateFilter($items, $state);
            }
            $items = $this->applySorting($items, $config, $sort, $direction);
            $totalItems = count($items);
            $totalPages = max(1, (int) ceil($totalItems / $perPage));
            $page = min($page, $totalPages);
            $offset = ($page - 1) * $perPage;
            $pageItems = array_slice($items, $offset, $perPage);
        }

        return $this->render('admin/crud/index.html.twig', [
            'resource' => $resource,
            'config' => $config,
            'rows' => $this->buildRows($pageItems, array_keys($config['list']), $this->getDisplayField($config)),
            'search' => $search,
            'state' => $state,
            'sort' => $sort,
            'direction' => $direction,
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
                'hasPrevious' => $page > 1,
                'hasNext' => $page < $totalPages,
            ],
            'filterDefinitions' => $filterDefinitions,
            'filters' => $this->extractFilters($request->query->all(), $filterDefinitions),
            'displayField' => $this->getDisplayField($config),
            'resourceSummary' => $this->buildResourceSummary($resource, $hasActiveFilters ? $items : [], $totalItems),
            'adminNavigation' => $this->adminNavigationBuilder->navigation(),
        ]);
    }

    #[Route('/{resource}/{id}', name: 'custom_admin_crud_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(string $resource, int $id): Response
    {
        $config = $this->crudRegistry->get($resource);
        $entity = $this->entityManager->getRepository($config['entity'])->find($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/crud/show.html.twig', [
            'resource' => $resource,
            'config' => $config,
            'item' => $entity,
            'itemLabel' => $this->getDisplayLabel($entity, $config),
            'displayField' => $this->getDisplayField($config),
            'detailRows' => $this->buildDetailRows($entity, $config),
            'adminNavigation' => $this->adminNavigationBuilder->navigation(),
        ]);
    }

    #[Route('/{resource}/new', name: 'custom_admin_crud_new', methods: ['GET', 'POST'])]
    public function new(string $resource, Request $request): Response
    {
        $config = $this->crudRegistry->get($resource);
        $entityClass = $config['entity'];
        $entity = new $entityClass();
        $form = $this->buildForm($resource, $entity, $config);
        $form->handleRequest($request);
        $this->syncRequestedImage($entity, $form);
        $this->rejectClosedRefreshmentOrder($entity, $form);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($entity instanceof Page) {
                $this->preparePage($entity, true);
                if (!$this->isPageSlugAvailable($entity)) {
                    $form->get('slug')->addError(new FormError('Ce slug est deja utilise par une autre page.'));
                } else {
                    $this->entityManager->persist($entity);
                    $this->entityManager->flush();
                    $this->afterFlush($entity);
                    $this->addFlash('success', sprintf('%s cree avec succes.', $config['singular']));

                    return $this->redirectAfterSave($request, $resource, $entity);
                }
            } elseif ($entity instanceof Merch) {
                if (!$this->validateMerchStocks($entity, $form)) {
                    // Leave the form open so the duplicate can be corrected before flush.
                } else {
                    $this->prepareEntity($entity, true);
                    $this->entityManager->persist($entity);
                    $this->entityManager->flush();
                    $this->afterFlush($entity);
                    $this->addFlash('success', sprintf('%s cree avec succes.', $config['singular']));

                    return $this->redirectAfterSave($request, $resource, $entity);
                }
            } else {
                $this->prepareEntity($entity, true);
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
                $this->afterFlush($entity);
                $this->addFlash('success', sprintf('%s cree avec succes.', $config['singular']));

                return $this->redirectAfterSave($request, $resource, $entity);
            }
        }

        return $this->render('admin/crud/form.html.twig', [
            'resource' => $resource,
            'config' => $config,
            'item' => $entity,
            'form' => $form,
            'mode' => 'new',
            'adminNavigation' => $this->adminNavigationBuilder->navigation(),
        ]);
    }

    #[Route('/{resource}/{id}/edit', name: 'custom_admin_crud_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(string $resource, int $id, Request $request): Response
    {
        $config = $this->crudRegistry->get($resource);
        $entity = $this->entityManager->getRepository($config['entity'])->find($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $form = $this->buildForm($resource, $entity, $config);
        $form->handleRequest($request);
        $this->syncRequestedImage($entity, $form);
        $this->rejectClosedRefreshmentOrder($entity, $form);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($entity instanceof Page) {
                $this->preparePage($entity, false);
                if (!$this->isPageSlugAvailable($entity)) {
                    $form->get('slug')->addError(new FormError('Ce slug est deja utilise par une autre page.'));
                } else {
                    $this->entityManager->flush();
                    $this->afterFlush($entity);
                    $this->addFlash('success', sprintf('%s modifie avec succes.', $config['singular']));

                    return $this->redirectAfterSave($request, $resource, $entity);
                }
            } elseif ($entity instanceof Merch) {
                if (!$this->validateMerchStocks($entity, $form)) {
                    // Leave the form open so the duplicate can be corrected before flush.
                } else {
                    $this->prepareEntity($entity, false);
                    $this->entityManager->flush();
                    $this->afterFlush($entity);
                    $this->addFlash('success', sprintf('%s modifie avec succes.', $config['singular']));

                    return $this->redirectAfterSave($request, $resource, $entity);
                }
            } else {
                $this->prepareEntity($entity, false);
                $this->entityManager->flush();
                $this->afterFlush($entity);
                $this->addFlash('success', sprintf('%s modifie avec succes.', $config['singular']));

                return $this->redirectAfterSave($request, $resource, $entity);
            }
        }

        return $this->render('admin/crud/form.html.twig', [
            'resource' => $resource,
            'config' => $config,
            'item' => $entity,
            'form' => $form,
            'mode' => 'edit',
            'adminNavigation' => $this->adminNavigationBuilder->navigation(),
        ]);
    }

    #[Route('/{resource}/{id}/delete', name: 'custom_admin_crud_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(string $resource, int $id, Request $request): Response
    {
        $config = $this->crudRegistry->get($resource);
        if (($config['deletable'] ?? true) === false) {
            throw $this->createNotFoundException();
        }

        $entity = $this->entityManager->getRepository($config['entity'])->find($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('admin_delete_'.$resource.'_'.$id, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Formulaire invalide, suppression annulee.');

            return $this->redirectToRoute('custom_admin_crud_index', array_merge($request->query->all(), ['resource' => $resource]));
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();
        $this->addFlash('success', sprintf('%s supprime avec succes.', $config['singular']));

        return $this->redirectToRoute('custom_admin_crud_index', array_merge($request->query->all(), ['resource' => $resource]));
    }

    #[Route('/{resource}/{id}/confirm', name: 'custom_admin_crud_confirm', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function confirm(string $resource, int $id, Request $request): Response
    {
        if ($resource !== 'commandes-merch') {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('admin_confirm_'.$resource.'_'.$id, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Formulaire invalide, confirmation annulee.');

            return $this->redirectToRoute('custom_admin_crud_index', array_merge($request->query->all(), ['resource' => $resource]));
        }

        $order = $this->entityManager->getRepository(MerchOrder::class)->find($id);
        if (!$order instanceof MerchOrder) {
            throw $this->createNotFoundException();
        }

        if (!$order->canBeConfirmed()) {
            $this->addFlash('info', sprintf('La commande #%d a deja ete confirmee ou executee.', $order->getId() ?? 0));

            return $this->redirectToRoute('custom_admin_crud_show', array_merge($request->query->all(), [
                'resource' => $resource,
                'id' => $id,
            ]));
        }

        $order->setConfirmedAt(new \DateTimeImmutable());
        $order->setConfirmedBy($this->getUser() instanceof User ? $this->getUser() : null);
        $this->entityManager->flush();

        $recipient = trim((string) ($order->getEmail() ?? $order->getUser()?->getUserIdentifier() ?? ''));
        if ($recipient !== '') {
            try {
                $this->mailService->send(
                    to: $recipient,
                    subject: sprintf('Commande merch #%d confirmee', $order->getId() ?? 0),
                    template: 'email/merch_order_ready.html.twig',
                    context: [
                        'order' => $order,
                    ]
                );
            } catch (\Throwable) {
                $this->addFlash('warning', 'Commande confirmee, mais l\'email de notification a echoue.');
            }
        }

        $this->addFlash('success', sprintf('Commande merch #%d confirmee.', $order->getId() ?? 0));

        return $this->redirectToRoute('custom_admin_crud_show', array_merge($request->query->all(), [
            'resource' => $resource,
            'id' => $id,
        ]));
    }

    #[Route('/pages/preview', name: 'custom_admin_page_preview', methods: ['POST'])]
    public function previewPage(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('page_preview', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $page = new Page();
        $page->setTitle(trim((string) $request->request->get('title', '')) ?: 'Apercu de page');
        $page->setSlug($this->normalizePageSlug((string) $request->request->get('slug', ''), (string) $request->request->get('title', '')));
        $page->setContent($this->pageContentSanitizer->sanitize((string) $request->request->get('content', '')));
        $page->setCreatedAt(new \DateTimeImmutable());

        $imageId = (int) $request->request->get('image_id', 0);
        if ($imageId > 0) {
            $image = $this->mediaRepository->find($imageId);
            if ($image instanceof Media) {
                $page->setImage($image);
            }
        }

        return $this->render('page/show.html.twig', [
            'page' => $page,
            'pagePreview' => true,
        ]);
    }

    #[Route('/articles/preview', name: 'custom_admin_post_preview', methods: ['POST'])]
    public function previewPost(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('post_preview', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $post = new Post();
        $title = trim((string) $request->request->get('title', '')) ?: 'Apercu d\'article';
        $slug = trim((string) $request->request->get('slug', ''));

        $post->setTitle($title);
        $post->setSlug($this->slug($slug !== '' ? $slug : $title));
        $post->setContent($this->pageContentSanitizer->sanitize((string) $request->request->get('content', '')));
        $post->setCreatedAt(new \DateTime());
        $post->setUpdatedAt(new \DateTime());

        $imageId = (int) $request->request->get('image_id', 0);
        if ($imageId > 0) {
            $image = $this->mediaRepository->find($imageId);
            if ($image instanceof Media) {
                $post->setImage($image);
            }
        }

        $categoryId = (int) $request->request->get('category_id', 0);
        if ($categoryId > 0) {
            $category = $this->entityManager->getRepository(EventCategory::class)->find($categoryId);
            if ($category instanceof EventCategory) {
                $post->setCategory($category);
            }
        }

        return $this->render('posts/show.html.twig', [
            'post' => $post,
            'postPreview' => true,
        ]);
    }

    #[Route('/evenements/preview', name: 'custom_admin_event_preview', methods: ['POST'])]
    public function previewEvent(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('event_preview', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $event = new Event();
        $event->setTitle(trim((string) $request->request->get('title', '')) ?: 'Apercu d evenement');

        $dateValue = trim((string) $request->request->get('date', ''));
        if ($dateValue !== '') {
            try {
                $event->setDate(new \DateTime($dateValue));
            } catch (\Throwable) {
                $event->setDate(new \DateTime());
            }
        } else {
            $event->setDate(new \DateTime());
        }

        $event->setOpponent(trim((string) $request->request->get('opponent', '')) ?: null);
        $matchLocation = trim((string) $request->request->get('matchLocation', ''));
        $event->setMatchLocation($matchLocation !== '' ? $matchLocation : null);
        $event->setSeason(trim((string) $request->request->get('season', '')) ?: null);

        $journee = trim((string) $request->request->get('journee', ''));
        $event->setJournee($journee !== '' ? (int) $journee : null);
        $event->setDescription($this->pageContentSanitizer->sanitize((string) $request->request->get('description', '')));

        $imageId = (int) $request->request->get('image_id', 0);
        if ($imageId > 0) {
            $image = $this->mediaRepository->find($imageId);
            if ($image instanceof Media) {
                $event->setImage($image);
            }
        }

        $mediaIds = array_values(array_filter(array_map('intval', preg_split('/\s*,\s*/', (string) $request->request->get('media_ids', '')) ?: [])));
        if ($mediaIds !== []) {
            $selectedMedia = [];
            foreach ($mediaIds as $mediaId) {
                $media = $this->mediaRepository->find($mediaId);
                if ($media instanceof Media) {
                    $selectedMedia[] = $media;
                }
            }

            if ($selectedMedia !== []) {
                $event->setMedia($selectedMedia);
            }
        }

        $categoryId = (int) $request->request->get('category_id', 0);
        if ($categoryId > 0) {
            $category = $this->entityManager->getRepository(EventCategory::class)->find($categoryId);
            if ($category instanceof EventCategory) {
                $event->setCategory($category);
            }
        }

        return $this->render('events/show.html.twig', [
            'event' => $event,
            'eventPreview' => true,
        ]);
    }

    #[Route('/groupe/preview', name: 'custom_admin_group_preview', methods: ['POST'])]
    public function previewGroup(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('group_preview', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $group = new GroupPage();
        $group->setName(trim((string) $request->request->get('name', '')) ?: 'Apercu du groupe');
        $group->setDescription($this->pageContentSanitizer->sanitize((string) $request->request->get('description', '')));
        $group->setHistoireText($this->pageContentSanitizer->sanitize((string) $request->request->get('histoireText', '')));
        $group->setMentaliteText($this->pageContentSanitizer->sanitize((string) $request->request->get('mentaliteText', '')));
        $group->setFonctionnementText($this->pageContentSanitizer->sanitize((string) $request->request->get('fonctionnementText', '')));
        $group->setRejoindreText($this->pageContentSanitizer->sanitize((string) $request->request->get('rejoindreText', '')));
        $group->setSeCarterText($this->pageContentSanitizer->sanitize((string) $request->request->get('seCarterText', '')));

        $logoId = (int) $request->request->get('logo_id', 0);
        if ($logoId > 0) {
            $logo = $this->mediaRepository->find($logoId);
            if ($logo instanceof Media) {
                $group->setLogo($logo);
            }
        }

        $bannerId = (int) $request->request->get('banner_id', 0);
        if ($bannerId > 0) {
            $banner = $this->mediaRepository->find($bannerId);
            if ($banner instanceof Media) {
                $group->setBanner($banner);
            }
        }

        return $this->render('group/show.html.twig', [
            'group' => $group,
            'groupPreview' => true,
        ]);
    }

    #[Route('/chants/preview', name: 'custom_admin_chant_preview', methods: ['POST'])]
    public function previewChant(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('chant_preview', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $chant = new Chant();
        $chant->setTitle(trim((string) $request->request->get('title', '')) ?: 'Apercu de chant');
        $chant->setLyrics($this->pageContentSanitizer->sanitize((string) $request->request->get('lyrics', '')));

        $audioPath = trim((string) $request->request->get('audioPath', ''));
        if ($audioPath !== '') {
            $chant->setAudioPath($audioPath);
        }
        $chant->setUpdatedAt(new \DateTimeImmutable());

        $audioLabel = trim((string) $request->request->get('audioLabel', ''));

        return $this->render('chants/show.html.twig', [
            'chant' => $chant,
            'chantPreview' => true,
            'chantPreviewAudioLabel' => $audioLabel !== '' ? $audioLabel : null,
        ]);
    }

    #[Route('/linktree/preview', name: 'custom_admin_linktree_preview', methods: ['POST'])]
    public function previewLinktree(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('linktree_preview', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $link = new LinktreeLink();
        $link->setTitle(trim((string) $request->request->get('title', '')) ?: 'Apercu de lien');
        $link->setDescription(trim((string) $request->request->get('description', '')) ?: null);
        $link->setUrl(trim((string) $request->request->get('url', '')) ?: '#');
        $link->setIcon(trim((string) $request->request->get('icon', '')) ?: null);
        $link->setPosition((int) $request->request->get('position', 0));
        $link->setEnabled($request->request->getBoolean('enabled', true));
        $link->setFeatured($request->request->getBoolean('featured', false));
        $link->setOpenInNewTab($request->request->getBoolean('openInNewTab', false));

        return $this->render('linktree/preview.html.twig', [
            'link' => $link,
            'linktreePreview' => true,
        ]);
    }

    #[Route('/medias/preview', name: 'custom_admin_media_preview', methods: ['POST'])]
    public function previewMedia(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('media_preview', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $media = new Media();
        $media->setPath(trim((string) $request->request->get('path', '')) ?: null);
        $media->setAlt(trim((string) $request->request->get('alt', '')) ?: null);

        return $this->render('media/preview.html.twig', [
            'media' => $media,
            'mediaPreview' => true,
        ]);
    }

    #[Route('/editor-scripts/page-editor.js', name: 'custom_admin_page_editor_js', methods: ['GET'])]
    public function pageEditorScript(): Response
    {
        return $this->serveEditorScript('assets/js/page-editor.js');
    }

    #[Route('/editor-scripts/event-editor.js', name: 'custom_admin_event_editor_js', methods: ['GET'])]
    public function eventEditorScript(): Response
    {
        return $this->serveEditorScript('assets/js/event-editor.js');
    }

    #[Route('/editor-scripts/media-editor.js', name: 'custom_admin_media_editor_js', methods: ['GET'])]
    public function mediaEditorScript(): Response
    {
        return $this->serveEditorScript('assets/js/media-editor.js');
    }

    #[Route('/editor-scripts/ticket-editor.js', name: 'custom_admin_ticket_editor_js', methods: ['GET'])]
    public function ticketEditorScript(): Response
    {
        return $this->serveEditorScript('assets/js/ticket-editor.js');
    }

    private function rejectClosedRefreshmentOrder(object $entity, \Symfony\Component\Form\FormInterface $form): void
    {
        if (!$form->isSubmitted() || !$entity instanceof \App\Entity\RefreshmentOrder) {
            return;
        }

        $matchDate = $entity->getTicket()?->getMatchDate();
        if ($matchDate !== null && new \DateTimeImmutable() >= \DateTimeImmutable::createFromInterface($matchDate)->modify('-24 hours')) {
            $form->addError(new FormError('Les precommandes de ce match sont closes depuis H-24. Modification interdite.'));
        }
    }

    private function buildForm(string $resource, object $entity, array $config): \Symfony\Component\Form\FormInterface
    {
        $builder = $this->formFactory->createNamedBuilder('admin_'.$resource, data: $entity);

        foreach ($config['fields'] as $name => $field) {
            $type = $field['type'];
            $options = $field;
            unset($options['type']);

            if ($entity instanceof Merch && $name === 'stocks' && isset($options['entry_options']) && is_array($options['entry_options'])) {
                $options['entry_options']['category_slug'] = $entity->getCategory()?->getSlug();
            }

            $builder->add($name, $type, $options);
        }

        if ($entity instanceof Page || $entity instanceof Post || $entity instanceof Ticket) {
            $builder->add('image_id', HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'data' => $entity->getImage()?->getId(),
            ]);
        }

        return $builder->getForm();
    }

    private function serveEditorScript(string $relativePath): Response
    {
        $filePath = dirname(__DIR__, 3).DIRECTORY_SEPARATOR.$relativePath;
        if (!is_file($filePath)) {
            throw $this->createNotFoundException();
        }

        $response = new Response((string) file_get_contents($filePath));
        $response->headers->set('Content-Type', 'application/javascript; charset=utf-8');
        $response->setPublic();
        $response->setMaxAge(0);
        $response->setSharedMaxAge(0);
        $response->headers->addCacheControlDirective('no-cache', true);

        return $response;
    }

    private function validateMerchStocks(Merch $merch, \Symfony\Component\Form\FormInterface $form): bool
    {
        $seenSizes = [];
        $duplicateSizes = [];

        foreach ($merch->getStocks() as $stock) {
            $size = trim($stock->getSize());
            if ($size === '') {
                continue;
            }

            if (isset($seenSizes[$size])) {
                $duplicateSizes[$size] = true;
                continue;
            }

            $seenSizes[$size] = true;
        }

        if ($duplicateSizes === []) {
            return true;
        }

        $form->addError(new FormError('Chaque taille de stock doit etre unique. Supprime les doublons avant d\'enregistrer.'));

        return false;
    }

    private function prepareEntity(object $entity, bool $new): void
    {
        if ($entity instanceof Post) {
            if (!$entity->getSlug()) {
                $entity->setSlug($this->slug((string) $entity->getTitle()));
            }
            if ($new && !$entity->getCreatedAt()) {
                $entity->setCreatedAt(new \DateTime());
            }
            $entity->setContent($this->pageContentSanitizer->sanitize($entity->getContent()));
            $entity->setUpdatedAt(new \DateTime());
        }

        if ($entity instanceof Page) {
            $entity->setSlug($this->normalizePageSlug((string) $entity->getSlug(), (string) $entity->getTitle()));
            $entity->setContent($this->pageContentSanitizer->sanitize($entity->getContent()));
            if ($new && !$entity->getCreatedAt()) {
                $entity->setCreatedAt(new \DateTime());
            }
        }

        if ($entity instanceof GroupPage) {
            $entity->setDescription($this->pageContentSanitizer->sanitize($entity->getDescription()));
            $entity->setHistoireText($this->pageContentSanitizer->sanitize($entity->getHistoireText()));
            $entity->setMentaliteText($this->pageContentSanitizer->sanitize($entity->getMentaliteText()));
            $entity->setFonctionnementText($this->pageContentSanitizer->sanitize($entity->getFonctionnementText()));
            $entity->setRejoindreText($this->pageContentSanitizer->sanitize($entity->getRejoindreText()));
            $entity->setSeCarterText($this->pageContentSanitizer->sanitize($entity->getSeCarterText()));
        }

        if ($entity instanceof Chant) {
            $entity->setLyrics($this->pageContentSanitizer->sanitize($entity->getLyrics()));
            $entity->setUpdatedAt(new \DateTimeImmutable());
        }

        if ($entity instanceof EventCategory && !$entity->getSlug()) {
            $entity->setSlug($this->slug((string) $entity->getName()));
        }

        if ($entity instanceof MerchCategory && !$entity->getSlug()) {
            $entity->setSlug($this->slug((string) $entity->getName()));
        }

        if ($entity instanceof TicketCategory && !$entity->getSlug()) {
            $entity->setSlug($this->slug((string) $entity->getName()));
        }

        if ($entity instanceof Media) {
            $entity->setUpdatedAt(new \DateTimeImmutable());
        }

        if ($entity instanceof MerchOrder && $new && !$entity->getCreatedAt()) {
            $entity->setCreatedAt(new \DateTime());
        }

        if ($entity instanceof TicketOrder && $new && !$entity->getCreatedAt()) {
            $entity->setCreatedAt(new \DateTime());
        }

        if ($entity instanceof PaymentCheckout && $new && !$entity->getCreatedAt()) {
            $entity->setCreatedAt(new \DateTime());
        }

        if ($entity instanceof CartageRegistration && $new) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }

        if ($entity instanceof BilletwebLead && $new) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }

        if ($entity instanceof User) {
            $plainPassword = trim((string) $entity->getPlainPassword());
            if ($plainPassword !== '') {
                $entity->setPassword($this->passwordHasher->hashPassword($entity, $plainPassword));
                $entity->eraseCredentials();
            } elseif ($new && !$entity->getPassword()) {
                $entity->setPassword($this->passwordHasher->hashPassword($entity, bin2hex(random_bytes(16))));
            }
        }
    }

    private function afterFlush(object $entity): void
    {
        if ($entity instanceof Merch) {
            $this->gadgetHistorySynchronizer->sync($entity, $this->entityManager);
        }
    }

    private function slug(string $value): string
    {
        $slug = $this->slugger->slug($value)->lower()->toString();

        return $slug !== '' ? $slug : 'element';
    }

    private function normalizePageSlug(string $slug, string $title): string
    {
        $candidate = trim($slug);
        if ($candidate === '') {
            $candidate = $this->slug($title);
        }

        return $this->slug($candidate);
    }

    private function isPageSlugAvailable(Page $page): bool
    {
        $existing = $this->pageRepository->findOneBy(['slug' => $page->getSlug()]);
        if (!$existing instanceof Page) {
            return true;
        }

        return $existing->getId() === $page->getId();
    }

    private function preparePage(Page $page, bool $new): void
    {
        $page->setSlug($this->normalizePageSlug((string) $page->getSlug(), (string) $page->getTitle()));
        $page->setContent($this->pageContentSanitizer->sanitize($page->getContent()));

        if ($new && !$page->getCreatedAt()) {
            $page->setCreatedAt(new \DateTime());
        }
    }

    private function syncRequestedImage(object $entity, \Symfony\Component\Form\FormInterface $form): void
    {
        if (
            !($entity instanceof Page)
            && !($entity instanceof Post)
            && !($entity instanceof Ticket)
        ) {
            return;
        }

        if (!$form->has('image_id')) {
            return;
        }

        $rawImageId = trim((string) $form->get('image_id')->getData());
        if ($rawImageId === '') {
            $entity->setImage(null);

            return;
        }

        $image = $this->mediaRepository->find((int) $rawImageId);
        if ($image instanceof Media) {
            $entity->setImage($image);
        }
    }

    private function redirectAfterSave(Request $request, string $resource, object $entity): Response
    {
        $parameters = array_merge($request->query->all(), ['resource' => $resource]);

        if ($request->request->has('save_and_continue') && method_exists($entity, 'getId')) {
            $id = $entity->getId();
            if ($id !== null) {
                return $this->redirectToRoute('custom_admin_crud_edit', array_merge($parameters, ['id' => $id]));
            }
        }

        return $this->redirectToRoute('custom_admin_crud_index', $parameters);
    }

    /**
     * @param object[] $items
     * @return object[]
     */
    private function applyMerchOrderStateFilter(array $items, string $state): array
    {
        if ($state === '') {
            return $items;
        }

        return array_values(array_filter($items, function (object $item) use ($state): bool {
            if (!$item instanceof MerchOrder) {
                return true;
            }

            return match ($state) {
                'pending_confirmation' => $item->canBeConfirmed(),
                'to_prepare' => $item->isConfirmed() && !$item->isExecuted(),
                'executed' => $item->isExecuted(),
                'confirmed' => $item->isConfirmed(),
                default => true,
            };
        }));
    }

    /**
     * @return array<int, array{label: string, value: int, tone: string, state?: string, resource?: string}>
     */
    private function buildResourceSummary(string $resource, array $items, ?int $totalItems = null): array
    {
        if ($resource === 'commandes-merch') {
            /** @var MerchOrderRepository $repository */
            $repository = $this->entityManager->getRepository(MerchOrder::class);

            return [
                [
                    'label' => 'À confirmer',
                    'value' => $repository->countPendingConfirmation(),
                    'tone' => 'warning',
                    'state' => 'pending_confirmation',
                ],
                [
                    'label' => 'À préparer',
                    'value' => $repository->countConfirmedAwaitingExecution(),
                    'tone' => 'info',
                    'state' => 'to_prepare',
                ],
                [
                    'label' => 'Exécutées',
                    'value' => $repository->count(['executed' => true]),
                    'tone' => 'success',
                    'state' => 'executed',
                ],
            ];
        }

        if ($resource === 'commandes-billets') {
            /** @var \App\Repository\TicketOrderRepository $repository */
            $repository = $this->entityManager->getRepository(TicketOrder::class);

            return [
                [
                    'label' => 'Payées',
                    'value' => $repository->count(['paid' => true]),
                    'tone' => 'success',
                ],
                [
                    'label' => 'À vérifier',
                    'value' => $repository->count(['paid' => false]),
                    'tone' => 'warning',
                ],
                [
                    'label' => 'Commandes affichées',
                    'value' => $totalItems ?? count($items),
                    'tone' => 'neutral',
                ],
            ];
        }

        if ($resource === 'paiements') {
            /** @var \App\Repository\PaymentCheckoutRepository $repository */
            $repository = $this->entityManager->getRepository(PaymentCheckout::class);

            return [
                [
                    'label' => 'En attente',
                    'value' => $repository->count(['status' => 'pending']),
                    'tone' => 'warning',
                ],
                [
                    'label' => 'Payés',
                    'value' => $repository->count(['status' => 'paid']),
                    'tone' => 'success',
                ],
                [
                    'label' => 'Échoués',
                    'value' => $repository->count(['status' => 'failed']),
                    'tone' => 'danger',
                ],
            ];
        }

        if ($resource === 'dons') {
            /** @var DonationRepository $repository */
            $repository = $this->entityManager->getRepository(Donation::class);

            return [
                [
                    'label' => 'Payés',
                    'value' => $repository->countByStatus(Donation::STATUS_PAID),
                    'tone' => 'success',
                ],
                [
                    'label' => 'En attente',
                    'value' => $repository->countByStatus(Donation::STATUS_PENDING),
                    'tone' => 'warning',
                ],
                [
                    'label' => 'CA dons',
                    'value' => $repository->sumToday(),
                    'formatted' => number_format($repository->sumToday(), 2, ',', ' ') . ' EUR',
                    'tone' => 'info',
                ],
            ];
        }

        return [];
    }

    /**
     * @return array<int, array{name: string, label: string, type: string, choices?: array<string, string>, entityClass?: string, value?: string, from?: string, to?: string}>
     */
    private function buildFilterDefinitions(array $config): array
    {
        $definitions = [];

        foreach ($config['fields'] as $name => $field) {
            $type = $field['type'] ?? null;
            $label = (string) ($field['label'] ?? ucfirst($name));

            if ($type === ChoiceType::class && isset($field['choices']) && is_array($field['choices']) && ($field['multiple'] ?? false) !== true) {
                $choices = [];
                foreach ($field['choices'] as $choiceLabel => $choiceValue) {
                    $choices[(string) $choiceLabel] = (string) $choiceValue;
                }

                $definitions[] = [
                    'name' => $name,
                    'label' => $label,
                    'type' => 'choice',
                    'choices' => $choices,
                ];
                continue;
            }

            if ($type === CheckboxType::class) {
                $definitions[] = [
                    'name' => $name,
                    'label' => $label,
                    'type' => 'boolean',
                ];
                continue;
            }

            if (($type === DateType::class || $type === DateTimeType::class) && in_array($name, ['createdAt', 'updatedAt', 'date', 'expiresAt', 'birthDate'], true)) {
                $definitions[] = [
                    'name' => $name,
                    'label' => $label,
                    'type' => 'date_range',
                ];
                continue;
            }

            if (
                $type === EntityType::class
                && in_array($name, ['category', 'user', 'event', 'gallery', 'merch', 'ticket', 'groupPage', 'payment', 'statusOwner'], true)
                && isset($field['class'])
                && is_string($field['class'])
            ) {
                $choices = $this->buildEntityFilterChoices($field['class'], $field['choice_label'] ?? null);
                if ($choices !== []) {
                    $definitions[] = [
                        'name' => $name,
                        'label' => $label,
                        'type' => 'entity',
                        'choices' => $choices,
                    ];
                }
            }

            if ($type === MoneyType::class) {
                $definitions[] = [
                    'name' => $name,
                    'label' => $label,
                    'type' => 'number_range',
                ];
            }
        }

        return $definitions;
    }

    /**
     * @param array<int, object> $items
     * @param array<int, array{name: string, label: string, type: string, choices?: array<string, string>, entityClass?: string, value?: string, from?: string, to?: string}> $filterDefinitions
     * @return array<int, object>
     */
    private function applyFilters(array $items, array $config, string $search, array $queryParams, array $filterDefinitions): array
    {
        if ($search !== '') {
            $items = array_values(array_filter($items, fn (object $item): bool => $this->matchesSearch($item, $config, $search)));
        }

        foreach ($filterDefinitions as $definition) {
            $name = $definition['name'];
            $type = $definition['type'];

            if ($type === 'date_range') {
                $from = trim((string) ($queryParams[$name.'_from'] ?? ''));
                $to = trim((string) ($queryParams[$name.'_to'] ?? ''));
                if ($from === '' && $to === '') {
                    continue;
                }

                $items = array_values(array_filter($items, function (object $item) use ($name, $from, $to): bool {
                    $value = $this->readValue($item, $name);
                    if (!$value instanceof \DateTimeInterface) {
                        return false;
                    }

                    if ($from !== '') {
                        $fromDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $from.' 00:00:00');
                        if ($fromDate instanceof \DateTimeImmutable && $value < $fromDate) {
                            return false;
                        }
                    }

                    if ($to !== '') {
                        $toDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $to.' 23:59:59');
                        if ($toDate instanceof \DateTimeImmutable && $value > $toDate) {
                            return false;
                        }
                    }

                    return true;
                }));

                continue;
            }

            if ($type === 'number_range') {
                $from = trim((string) ($queryParams[$name.'_min'] ?? ''));
                $to = trim((string) ($queryParams[$name.'_max'] ?? ''));
                if ($from === '' && $to === '') {
                    continue;
                }

                $items = array_values(array_filter($items, function (object $item) use ($name, $from, $to): bool {
                    $value = $this->readValue($item, $name);
                    if (!is_numeric($value)) {
                        return false;
                    }

                    $numeric = (float) $value;
                    if ($from !== '' && $numeric < (float) str_replace(',', '.', str_replace(' ', '', $from))) {
                        return false;
                    }

                    if ($to !== '' && $numeric > (float) str_replace(',', '.', str_replace(' ', '', $to))) {
                        return false;
                    }

                    return true;
                }));

                continue;
            }

            $value = trim((string) ($queryParams[$name] ?? ''));
            if ($value === '') {
                continue;
            }

            $items = array_values(array_filter($items, function (object $item) use ($name, $type, $value): bool {
                $rawValue = $this->readValue($item, $name);

                return match ($type) {
                    'boolean' => $this->matchesBooleanFilter($rawValue, $value),
                    'entity' => $this->matchesEntityFilter($rawValue, $value),
                    default => $this->matchesChoiceFilter($rawValue, $value),
                };
            }));
        }

        return $items;
    }

    /**
     * @param array<int, object> $items
     * @return array<int, object>
     */
    private function applySorting(array $items, array $config, string $sort, string $direction): array
    {
        if ($sort === '' || !array_key_exists($sort, $config['list'])) {
            return $items;
        }

        usort($items, function (object $left, object $right) use ($sort, $direction): int {
            $leftValue = $this->normalizeSortValue($this->readValue($left, $sort));
            $rightValue = $this->normalizeSortValue($this->readValue($right, $sort));

            $comparison = $leftValue <=> $rightValue;

            return $direction === 'desc' ? -$comparison : $comparison;
        });

        return $items;
    }

    /**
     * @return array<int, array{name: string, label: string, type: string, choices?: array<string, string>}>
     */
    private function extractFilters(array $queryParams, array $filterDefinitions): array
    {
        $filters = [];

        foreach ($filterDefinitions as $definition) {
            if ($definition['type'] === 'date_range') {
                $filters[] = [
                    'name' => $definition['name'],
                    'label' => $definition['label'],
                    'type' => 'date_range',
                    'from' => trim((string) ($queryParams[$definition['name'].'_from'] ?? '')),
                    'to' => trim((string) ($queryParams[$definition['name'].'_to'] ?? '')),
                ];
                continue;
            }

            if ($definition['type'] === 'number_range') {
                $filters[] = [
                    'name' => $definition['name'],
                    'label' => $definition['label'],
                    'type' => 'number_range',
                    'from' => trim((string) ($queryParams[$definition['name'].'_min'] ?? '')),
                    'to' => trim((string) ($queryParams[$definition['name'].'_max'] ?? '')),
                ];
                continue;
            }

            $filters[] = [
                'name' => $definition['name'],
                'label' => $definition['label'],
                'type' => $definition['type'],
                'choices' => $definition['choices'] ?? [],
                'value' => trim((string) ($queryParams[$definition['name']] ?? '')),
            ];
        }

        return $filters;
    }

    /**
     * @return array<int, array{name: string, label: string, type: string, value: mixed}>
     */
    private function buildDetailRows(object $entity, array $config): array
    {
        $rows = [];

        foreach ($config['fields'] as $name => $field) {
            if (in_array($name, ['plainPassword', 'password'], true)) {
                continue;
            }

            $rows[] = [
                'name' => $name,
                'label' => (string) ($field['label'] ?? ucfirst($name)),
                'type' => $field['type'] ?? TextType::class,
                'value' => $this->formatValue($this->readValue($entity, $name)),
            ];
        }

        return $rows;
    }

    private function getDisplayField(array $config): string
    {
        foreach (['title', 'name', 'email', 'label', 'slug'] as $candidate) {
            if (array_key_exists($candidate, $config['fields'])) {
                return $candidate;
            }
        }

        foreach (array_keys($config['list']) as $candidate) {
            if ($candidate !== 'id') {
                return $candidate;
            }
        }

        return 'id';
    }

    private function getDisplayLabel(object $item, array $config): string
    {
        $field = $this->getDisplayField($config);
        $value = $this->formatValue($this->readValue($item, $field));

        if ($value !== '') {
            return $value;
        }

        $id = $this->formatValue($this->readValue($item, 'id'));

        return $id !== '' ? '#'.$id : 'Element';
    }

    /**
     * @return array<string, string>
     */
    private function buildEntityFilterChoices(string $entityClass, mixed $choiceLabel = null): array
    {
        $repository = $this->entityManager->getRepository($entityClass);
        $choices = [];

        foreach ($repository->findBy([], ['id' => 'ASC']) as $entity) {
            if (!is_object($entity)) {
                continue;
            }

            $label = $this->resolveChoiceLabel($entity, $choiceLabel);
            $id = $this->formatValue($this->readValue($entity, 'id'));
            if ($id === '') {
                continue;
            }

            $choices[$label] = $id;
        }

        return $choices;
    }

    private function resolveChoiceLabel(object $entity, mixed $choiceLabel): string
    {
        if (is_string($choiceLabel) && $choiceLabel !== '') {
            $value = $this->readValue($entity, $choiceLabel);
            $label = $this->formatValue($value);
            if ($label !== '') {
                return $label;
            }
        }

        if (is_callable($choiceLabel)) {
            $label = (string) $choiceLabel($entity);
            if ($label !== '') {
                return $label;
            }
        }

        if (method_exists($entity, '__toString')) {
            $label = trim((string) $entity);
            if ($label !== '') {
                return $label;
            }
        }

        $id = $this->formatValue($this->readValue($entity, 'id'));

        return $id !== '' ? '#'.$id : 'Element';
    }

    private function matchesSearch(object $item, array $config, string $search): bool
    {
        $needle = $this->normalizeSearchTerm($search);
        $fields = array_keys($config['list']);
        foreach ($fields as $field) {
            $value = $this->normalizeSearchTerm($this->formatValue($this->readValue($item, $field)));
            if ($value !== '' && str_contains($value, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function matchesChoiceFilter(mixed $value, string $filterValue): bool
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d') === $filterValue;
        }

        if (is_object($value) && method_exists($value, 'getId')) {
            return (string) $value->getId() === $filterValue;
        }

        return trim($this->formatValue($value)) === $filterValue;
    }

    private function matchesBooleanFilter(mixed $value, string $filterValue): bool
    {
        if ($filterValue === '1') {
            return (bool) $value === true;
        }

        if ($filterValue === '0') {
            return (bool) $value === false;
        }

        return true;
    }

    private function matchesEntityFilter(mixed $value, string $filterValue): bool
    {
        if (is_object($value) && method_exists($value, 'getId')) {
            return (string) $value->getId() === $filterValue;
        }

        if (is_countable($value)) {
            foreach ($value as $item) {
                if (is_object($item) && method_exists($item, 'getId') && (string) $item->getId() === $filterValue) {
                    return true;
                }
            }

            return false;
        }

        return trim($this->formatValue($value)) === $filterValue;
    }

    private function normalizeSortValue(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->getTimestamp();
        }

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        if (is_object($value) && method_exists($value, 'getId')) {
            return $value->getId();
        }

        if (is_scalar($value) || $value === null) {
            return mb_strtolower(trim((string) $value));
        }

        if (is_countable($value)) {
            return count($value);
        }

        return mb_strtolower($this->formatValue($value));
    }

    /**
     * @param object[] $items
     * @param string[] $fields
     * @return array<int, array{item: object, values: array<string, string>}>
     */
    private function buildRows(array $items, array $fields, string $displayField): array
    {
        $rows = [];

        foreach ($items as $item) {
            $values = [];
            foreach ($fields as $field) {
                $values[$field] = $this->formatValue($this->readValue($item, $field));
            }

            $rows[] = [
                'item' => $item,
                'values' => $values,
                'label' => $this->formatValue($this->readValue($item, $displayField)),
            ];
        }

        return $rows;
    }

    private function readValue(object $item, string $field): mixed
    {
        $method = 'get'.str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
        if (method_exists($item, $method)) {
            return $item->{$method}();
        }

        $method = 'is'.str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
        if (method_exists($item, $method)) {
            return $item->{$method}();
        }

        return null;
    }

    private function formatValue(mixed $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('d/m/Y H:i');
        }

        if (is_float($value)) {
            return number_format($value, 2, ',', ' ');
        }

        if (is_bool($value)) {
            return $value ? 'Oui' : 'Non';
        }

        if (is_scalar($value) || $value === null) {
            return trim((string) $value);
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        if (is_countable($value)) {
            return (string) count($value);
        }

        return '';
    }

    private function normalizeSearchTerm(string $value): string
    {
        $value = mb_strtolower(trim($value));

        if ($value === '') {
            return '';
        }

        if (class_exists(\Normalizer::class)) {
            $normalized = \Normalizer::normalize($value, \Normalizer::FORM_D);
            if (is_string($normalized)) {
                $value = preg_replace('/\p{Mn}+/u', '', $normalized) ?? $normalized;
            }
        } elseif (function_exists('iconv')) {
            $transliterated = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
            if (is_string($transliterated) && $transliterated !== '') {
                $value = $transliterated;
            }
        }

        return preg_replace('/[^a-z0-9]+/u', '', $value) ?? $value;
    }
}
