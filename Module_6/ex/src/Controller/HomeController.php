<?php
namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PostRepository $postRepository, Request $request): Response
    {
        $session = $request->getSession();
        
        // Si la session est nouvelle ou n'a pas de nom anonyme
        if (!$session->isStarted() || !$session->has('anonymousName')) {
            $session->start();
            $session->set('anonymousName', $this->getRandomAnonymousName());
        }
    
        // Récupère le temps écoulé
        $timeElapsed = time() - $session->get('lastInteraction', time());
    
        // Récupère les posts (pagination par défaut page 1, 10 éléments)
        $page = $request->query->getInt('page', 1);
        $posts = $postRepository->findPaginated($page, 10);
    
        return $this->render('home/index.html.twig', [
            'anonymousName' => $session->get('anonymousName'),
            'timeElapsed' => $timeElapsed,
            'posts' => $posts['items'],
            'nextPage' => $posts['hasNext'] ? $page + 1 : null,
            'previousPage' => $page > 1 ? $page - 1 : null,
            'currentUser' => $this->getUser(),
        ]);
    }

    private function getRandomAnonymousName(): string
    {
        $animals = ['dog', 'cat', 'rabbit', 'fox', 'bear', 'lion', 'tiger', 'wolf', 'eagle'];
        $randomAnimal = $animals[array_rand($animals)];
        return 'Anonymous ' . ucfirst($randomAnimal);
    }
}
