<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/actualites')]
class PostController extends AbstractController
{
    #[Route('/', name: 'post_index')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findPublicPosts();

        return $this->render('posts/list.html.twig', [
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

        return $this->render('posts/show.html.twig', [
            'post' => $post,
        ]);
    }
}
