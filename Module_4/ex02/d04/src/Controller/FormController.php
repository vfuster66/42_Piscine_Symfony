<?php

namespace App\Controller;

use App\Form\MessageFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FormController extends AbstractController
{
    private string $notesFilePath;

    public function __construct(ParameterBagInterface $params)
    {
        $this->notesFilePath = $params->get('notes_file_path');
    }

    #[Route('/e02', name: 'form_example')]
    public function formExample(Request $request): Response
    {
        $form = $this->createForm(MessageFormType::class);

        $form->handleRequest($request);

        $lastLine = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = $data['message'];
            $includeTimestamp = $data['includeTimestamp'];

            $line = $message;
            if ($includeTimestamp) {
                $line .= ' - ' . date('Y-m-d H:i:s');
            }

            if (!file_exists($this->notesFilePath)) {
                touch($this->notesFilePath);
            }

            file_put_contents($this->notesFilePath, $line . PHP_EOL, FILE_APPEND);

            $lastLine = $line;
        }

        return $this->render('e02/form.html.twig', [
            'form' => $form->createView(),
            'lastLine' => $lastLine,
        ]);
    }
}
