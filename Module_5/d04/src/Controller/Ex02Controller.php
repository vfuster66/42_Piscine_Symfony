<?php

namespace App\Controller;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class Ex02Controller extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/ex02/create-table', name: 'ex02_create_table')]
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

            return new Response('✅ Table "persons" created successfully!');
        } catch (DBALException $e) {
            $this->logger->error('Database error: ' . $e->getMessage());
            return new Response('❌ Database error: ' . $e->getMessage(), 500);
        }
    }

    #[Route('/ex02/insert-data', name: 'ex02_insert_data', methods: ['GET', 'POST'])]
    public function insertData(Request $request): Response
    {
        $message = '';

        if ($request->isMethod('POST')) {
            try {
                $connection = $this->entityManager->getConnection();
                $sql = "
                    INSERT INTO persons (username, name, email, enable, birthdate, address)
                    VALUES (:username, :name, :email, :enable, :birthdate, :address)
                ";

                $connection->executeStatement($sql, [
                    'username' => $request->request->get('username'),
                    'name' => $request->request->get('name'),
                    'email' => $request->request->get('email'),
                    'enable' => (bool) $request->request->get('enable'),
                    'birthdate' => $request->request->get('birthdate'),
                    'address' => $request->request->get('address'),
                ]);

                $message = '✅ Data inserted successfully!';
            } catch (DBALException $e) {
                $this->logger->error('Database error: ' . $e->getMessage());
                $message = '❌ Database error: ' . $e->getMessage();
            }
        }

        return $this->render('ex02/insert.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/ex02/view-data', name: 'ex02_view_data', methods: ['GET'])]
    public function viewData(): Response
    {
        try {
            $connection = $this->entityManager->getConnection();
            $sql = "SELECT * FROM persons";
            $persons = $connection->fetchAllAssociative($sql);
    
            return $this->render('ex02/view.html.twig', [
                'persons' => $persons,
            ]);
        } catch (DBALException $e) {
            $this->logger->error('Database error: ' . $e->getMessage());
            return new Response('❌ Database error: ' . $e->getMessage(), 500);
        }
    }
    
}
