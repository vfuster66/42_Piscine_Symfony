<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Ex03Controller extends AbstractController
{
    #[Route('/{_locale}/ex03', 
        name: 'ex03',
        requirements: [
            '_locale' => 'en|fr'
        ],
        defaults: [
            '_locale' => 'en'
        ]
    )]
    public function extensionAction(): Response
    {
        return $this->render('ex03.html.twig');
    }
}