<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticlesController extends AbstractController
{
    private array $categories = [
        'gull' => 'Gulls are seabirds in the family Laridae.',
        'dolphin' => 'Dolphins are aquatic mammals known for their intelligence.',
        'penguin' => 'Penguins are flightless birds that live in the Southern Hemisphere.',
    ];

    #[Route('/e01', name: 'articles_index')]
    public function index(): Response
    {
        return $this->render('e01/index.html.twig', [
            'categories' => array_keys($this->categories),
        ]);
    }

    #[Route('/e01/{category}', name: 'article_show')]
    public function show(string $category): Response
    {
        if (!isset($this->categories[$category])) {
            return $this->redirectToRoute('articles_index');
        }

        return $this->render("e01/$category.html.twig", [
            'content' => $this->categories[$category],
        ]);
    }
}
