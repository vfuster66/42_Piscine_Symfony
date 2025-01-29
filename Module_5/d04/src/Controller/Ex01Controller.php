<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception as DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class Ex01Controller extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/ex01/create-table', name: 'ex01_create_table')]
    public function createTable(): Response
    {
        try {
            // Générer un message de succès
            $message = '✅ Table "persons" created successfully using Doctrine ORM!';

            // Rendu de la vue Twig
            return $this->render('ex01/index.html.twig', [
                'message' => $message,
            ]);
        } catch (DBALException $e) {
            // Gestion des erreurs liées à la base de données
            $this->logger->error('Database error: ' . $e->getMessage());
            $errorMessage = '❌ A database error occurred: ' . $e->getMessage();

            return $this->render('ex01/index.html.twig', [
                'message' => $errorMessage,
            ]);
        } catch (\Exception $e) {
            // Gestion des erreurs générales
            $this->logger->critical('Unexpected error: ' . $e->getMessage());
            $errorMessage = '❌ An unexpected error occurred: ' . $e->getMessage();

            return $this->render('ex01/index.html.twig', [
                'message' => $errorMessage,
            ]);
        }
    }
}
