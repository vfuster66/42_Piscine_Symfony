<?php

namespace App\Controller;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class Ex06Controller extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/ex06/create-table', name: 'ex06_create_table')]
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

    #[Route('/ex06/insert-data', name: 'ex06_insert_data', methods: ['GET', 'POST'])]
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

        return $this->render('ex06/insert.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/ex06/view-data', name: 'ex06_view_data', methods: ['GET'])]
    public function viewData(): Response
    {
        try {
            $connection = $this->entityManager->getConnection();
            $sql = "SELECT * FROM persons";
            $persons = $connection->fetchAllAssociative($sql);
    
            return $this->render('ex06/view.html.twig', [
                'persons' => $persons,
            ]);
        } catch (DBALException $e) {
            $this->logger->error('Database error: ' . $e->getMessage());
            return new Response('❌ Database error: ' . $e->getMessage(), 500);
        }
    }

    #[Route('/ex06/delete/{id}', name: 'ex06_delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        try {
            $connection = $this->entityManager->getConnection();
    
            // Vérifier si l'entrée existe
            $sqlCheck = "SELECT * FROM persons WHERE id = :id";
            $person = $connection->fetchAssociative($sqlCheck, ['id' => $id]);
    
            if (!$person) {
                $this->addFlash('error', '❌ La personne avec l\'ID ' . $id . ' n\'existe pas.');
                return $this->redirectToRoute('ex06_view_data');
            }
    
            // Supprimer l'entrée
            $sqlDelete = "DELETE FROM persons WHERE id = :id";
            $connection->executeStatement($sqlDelete, ['id' => $id]);
    
            $this->addFlash('success', '✅ La personne a été supprimée avec succès !');
        } catch (DBALException $e) {
            $this->logger->error('Database error: ' . $e->getMessage());
            $this->addFlash('error', '❌ Erreur lors de la suppression : ' . $e->getMessage());
        }
    
        return $this->redirectToRoute('ex06_view_data');
    }    

    #[Route('/ex06/edit/{id}', name: 'ex06_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $connection = $this->entityManager->getConnection();

        try {
            // Vérifier si l'entrée existe
            $sqlCheck = "SELECT * FROM persons WHERE id = :id";
            $person = $connection->fetchAssociative($sqlCheck, ['id' => $id]);

            if (!$person) {
                $this->addFlash('error', '❌ La personne avec l\'ID ' . $id . ' n\'existe pas.');
                return $this->redirectToRoute('ex06_view_data');
            }

            if ($request->isMethod('POST')) {
                $sqlUpdate = "
                    UPDATE persons
                    SET username = :username,
                        name = :name,
                        email = :email,
                        enable = :enable,
                        birthdate = :birthdate,
                        address = :address
                    WHERE id = :id
                ";

                $connection->executeStatement($sqlUpdate, [
                    'id' => $id,
                    'username' => $request->request->get('username'),
                    'name' => $request->request->get('name'),
                    'email' => $request->request->get('email'),
                    'enable' => (bool) $request->request->get('enable'),
                    'birthdate' => $request->request->get('birthdate'),
                    'address' => $request->request->get('address'),
                ]);

                $this->addFlash('success', '✅ Les informations ont été mises à jour avec succès.');
                return $this->redirectToRoute('ex06_view_data');
            }

            return $this->render('ex06/edit.html.twig', [
                'person' => $person,
            ]);
        } catch (DBALException $e) {
            $this->logger->error('Database error: ' . $e->getMessage());
            $this->addFlash('error', '❌ Erreur lors de la mise à jour : ' . $e->getMessage());
            return $this->redirectToRoute('ex06_view_data');
        }
    }
}
