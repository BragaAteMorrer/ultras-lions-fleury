<php

namespace App\Controller;

use App\Repository\LinktreeLinkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LinktreeController extends AbstractController
{
    #[Route('/liens', name: 'linktree_index')]
    public function index(LinktreeLinkRepository $linkRepository): Response
    {
        return $this->render('linktree/index.html.twig', [
            'links' => $linkRepository->findEnabledLinks(),
        ]);
    }
}
