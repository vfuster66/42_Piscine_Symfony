<?php

namespace App\Controller;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class Ex00Controller extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    // Affiche la page avec le lien "Créer la table"
    #[Route('/ex00', name: 'ex00_index')]
    public function index(): Response
    {
        return $this->render('ex00/index.html.twig', [
            'message' => null, // Pas de message par défaut
        ]);
    }

    // Crée la table et affiche un message de succès ou d'erreur
    #[Route('/ex00/create-table', name: 'ex00_create_table')]
    public function createTable(): Response
    {
        try {
            $connection = $this->entityManager->getConnection();

            $sql = "
                CREATE TABLE IF NOT EXISTS persons (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(255) UNIQUE NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    enable BOOLEAN NOT NULL,
                    birthdate DATETIME NOT NULL,
                    address LONGTEXT NOT NULL
                )
            ";

            $connection->executeStatement($sql);

            $message = '✅ Table "persons" created successfully!';
        } catch (DBALException $e) {
            $this->logger->error('Database error: ' . $e->getMessage());
            $message = '❌ A database error occurred: ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->logger->critical('Unexpected error: ' . $e->getMessage());
            $message = '❌ An unexpected error occurred: ' . $e->getMessage();
        }

        // Retourne à la page d'accueil de l'exercice avec un message
        return $this->render('ex00/index.html.twig', [
            'message' => $message,
        ]);
    }
}
