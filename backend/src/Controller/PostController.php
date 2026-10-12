<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/actualites')]
class PostController extends AbstractController
{
    #[Route('/', name: 'post_index')]
    public function index(
        Request $request,
        PostRepository $postRepository,
    ): Response
    {
        $searchTerm = trim((string) $request->query->get('q', ''));
        $selectedCategorySlug = trim((string) $request->query->get('category', ''));
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 9;

        $availableCategories = $postRepository->findPublicCategories();
        $selectedCategory = null;
        foreach ($availableCategories as $category) {
            if ($category->getSlug() === $selectedCategorySlug) {
                $selectedCategory = $category;
                break;
            }
        }

        $hasFilters = $searchTerm !== '' || $selectedCategorySlug !== '';
        $totalPosts = $postRepository->countPublicPosts(
            $searchTerm !== '' ? $searchTerm : null,
            $selectedCategorySlug !== '' ? $selectedCategorySlug : null
        );

        $featuredPost = null;
        $posts = [];
        $paginationTotal = $totalPosts;
        $offset = ($page - 1) * $perPage;

        if (!$hasFilters) {
            $featuredPost = $postRepository->findPublicPostsPage(1, 0)[0] ?? null;
            $paginationTotal = max(0, $totalPosts - ($featuredPost ? 1 : 0));
            $totalPages = max(1, (int) ceil($paginationTotal / $perPage));
            $page = min($page, $totalPages);

            $offset = $featuredPost ? 1 + (($page - 1) * $perPage) : (($page - 1) * $perPage);
            $posts = $postRepository->findPublicPostsPage(
                $perPage,
                $offset,
                null,
                null,
                $featuredPost && $featuredPost->getId() !== null ? [$featuredPost->getId()] : null
            );
        } else {
            $totalPages = max(1, (int) ceil(max(1, $totalPosts) / $perPage));
            $page = min($page, $totalPages);
            $offset = ($page - 1) * $perPage;
            $posts = $postRepository->findPublicPostsPage(
                $perPage,
                $offset,
                $searchTerm !== '' ? $searchTerm : null,
                $selectedCategorySlug !== '' ? $selectedCategorySlug : null
            );
        }

        $latestPost = $postRepository->findPublicPostsPage(1, 0)[0] ?? null;
        return $this->render('posts/list.html.twig', [
            'availableCategories' => $availableCategories,
            'latestPost' => $latestPost,
            'page' => $page,
            'pageTotal' => $totalPosts,
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $hasFilters ? $totalPosts : $paginationTotal,
                'totalPages' => $totalPages,
                'hasPrevious' => $page > 1,
                'hasNext' => $page < $totalPages,
                'query' => array_filter([
                    'q' => $searchTerm !== '' ? $searchTerm : null,
                    'category' => $selectedCategorySlug !== '' ? $selectedCategorySlug : null,
                ], static fn ($value) => $value !== null && $value !== ''),
            ],
            'selectedCategory' => $selectedCategory,
            'selectedCategorySlug' => $selectedCategorySlug,
            'searchTerm' => $searchTerm,
            'hasFilters' => $hasFilters,
            'featuredPost' => $featuredPost,
            'posts' => $posts,
        ]);
    }

    #[Route('/{slug}', name: 'post_show')]
    public function show(string $slug, PostRepository $postRepository): Response
    {
        $post = $postRepository->findPublicBySlug($slug);
        if (!$post) {
            throw $this->createNotFoundException();
        }

        $relatedPosts = $postRepository->findPublicRelatedPosts($post, 3);

        return $this->render('posts/show.html.twig', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
