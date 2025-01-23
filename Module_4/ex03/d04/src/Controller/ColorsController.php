<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ColorsController extends AbstractController
{
    private int $numberOfColors;

    public function __construct(ParameterBagInterface $params)
    {
        $this->numberOfColors = $params->get('e03.number_of_colors');
    }

    #[Route('/e03', name: 'colors_table')]
    public function showColors(): Response
    {
        $colors = ['black', 'red', 'blue', 'green'];
        $shades = [];

        foreach ($colors as $color) {
            $shades[$color] = $this->generateShades($color);
        }

        return $this->render('e03/colors.html.twig', [
            'shades' => $shades,
            'numberOfColors' => $this->numberOfColors,
        ]);
    }

    private function generateShades(string $color): array
    {
        $shades = [];
        $minIntensity = 50; // Valeur minimale pour éviter un noir trop sombre
        $maxIntensity = 255;

        for ($i = 0; $i < $this->numberOfColors; $i++) {
            $intensity = (int)($minIntensity + ($maxIntensity - $minIntensity) * $i / ($this->numberOfColors - 1));
            switch ($color) {
                case 'black':
                    $shades[] = sprintf('rgb(%d, %d, %d)', $intensity, $intensity, $intensity);
                    break;
                case 'red':
                    $shades[] = sprintf('rgb(%d, 0, 0)', $intensity);
                    break;
                case 'blue':
                    $shades[] = sprintf('rgb(0, 0, %d)', $intensity);
                    break;
                case 'green':
                    $shades[] = sprintf('rgb(0, %d, 0)', $intensity);
                    break;
            }
        }
        return $shades;
    }

}
