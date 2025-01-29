<?php

namespace App\Controller;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class Ex08Controller extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/ex08/create-table', name: 'ex08_create_table')]
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

    #[Route('/ex08/insert-data', name: 'ex08_insert_data', methods: ['GET', 'POST'])]
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

        return $this->render('ex08/insert.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/ex08/view-data', name: 'ex08_view_data', methods: ['GET'])]
    public function viewData(): Response
    {
        try {
            $connection = $this->entityManager->getConnection();
            $sql = "SELECT * FROM persons";
            $persons = $connection->fetchAllAssociative($sql);
    
            return $this->render('ex08/view.html.twig', [
                'persons' => $persons,
            ]);
        } catch (DBALException $e) {
            $this->logger->error('Database error: ' . $e->getMessage());
            return new Response('❌ Database error: ' . $e->getMessage(), 500);
        }
    }

    #[Route('/ex08/delete/{id}', name: 'ex08_delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        try {
            $connection = $this->entityManager->getConnection();
    
            // Vérifier si l'entrée existe
            $sqlCheck = "SELECT * FROM persons WHERE id = :id";
            $person = $connection->fetchAssociative($sqlCheck, ['id' => $id]);
    
            if (!$person) {
                $this->addFlash('error', '❌ La personne avec l\'ID ' . $id . ' n\'existe pas.');
                return $this->redirectToRoute('ex08_view_data');
            }
    
            // Supprimer l'entrée
            $sqlDelete = "DELETE FROM persons WHERE id = :id";
            $connection->executeStatement($sqlDelete, ['id' => $id]);
    
            $this->addFlash('success', '✅ La personne a été supprimée avec succès !');
        } catch (DBALException $e) {
            $this->logger->error('Database error: ' . $e->getMessage());
            $this->addFlash('error', '❌ Erreur lors de la suppression : ' . $e->getMessage());
        }
    
        return $this->redirectToRoute('ex08_view_data');
    }    

    #[Route('/ex08/edit/{id}', name: 'ex08_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $connection = $this->entityManager->getConnection();

        try {
            // Vérifier si l'entrée existe
            $sqlCheck = "SELECT * FROM persons WHERE id = :id";
            $person = $connection->fetchAssociative($sqlCheck, ['id' => $id]);

            if (!$person) {
                $this->addFlash('error', '❌ La personne avec l\'ID ' . $id . ' n\'existe pas.');
                return $this->redirectToRoute('ex08_view_data');
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
                return $this->redirectToRoute('ex08_view_data');
            }

            return $this->render('ex08/edit.html.twig', [
                'person' => $person,
            ]);
        } catch (DBALException $e) {
            $this->logger->error('Database error: ' . $e->getMessage());
            $this->addFlash('error', '❌ Erreur lors de la mise à jour : ' . $e->getMessage());
            return $this->redirectToRoute('ex08_view_data');
        }
    }

    #[Route('/ex08/alter-persons-table', name: 'ex08_alter_persons_table')]
    public function alterPersonsTable(): Response
    {
        try {
            $connection = $this->entityManager->getConnection();
            $sql = "
                ALTER TABLE persons
                ADD COLUMN marital_status ENUM('single', 'married', 'widower') NOT NULL DEFAULT 'single'
            ";
            $connection->executeStatement($sql);
    
            return new Response('✅ Column "marital_status" added successfully to "persons" table!');
        } catch (DBALException $e) {
            $this->logger->error('Database error: ' . $e->getMessage());
            return new Response('❌ Database error: ' . $e->getMessage(), 500);
        }
    }    

    #[Route('/ex08/create-related-tables', name: 'ex08_create_related_tables')]
    public function createRelatedTables(): Response
    {
        try {
            $connection = $this->entityManager->getConnection();

            // Create "bank_accounts" table
            $sqlBankAccounts = "
                CREATE TABLE IF NOT EXISTS bank_accounts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    person_id INT NOT NULL,
                    account_number VARCHAR(255) UNIQUE NOT NULL,
                    balance DECIMAL(10, 2) NOT NULL DEFAULT 0,
                    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE
                )
            ";
            $connection->executeStatement($sqlBankAccounts);

            // Create "addresses" table
            $sqlAddresses = "
                CREATE TABLE IF NOT EXISTS addresses (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    person_id INT NOT NULL,
                    address_line VARCHAR(255) NOT NULL,
                    city VARCHAR(255) NOT NULL,
                    postal_code VARCHAR(20) NOT NULL,
                    country VARCHAR(255) NOT NULL,
                    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE
                )
            ";
            $connection->executeStatement($sqlAddresses);

            return new Response('✅ Tables "bank_accounts" and "addresses" created successfully with relationships!');
        } catch (DBALException $e) {
            $this->logger->error('Database error: ' . $e->getMessage());
            return new Response('❌ Database error: ' . $e->getMessage(), 500);
        }
    }

    #[Route('/ex08/view-relations', name: 'ex08_view_relations')]
    public function viewRelations(): Response
    {
        try {
            $connection = $this->entityManager->getConnection();
    
            // Données pour la table "persons"
            $persons = $connection->fetchAllAssociative("
                SELECT id AS person_id, username, name, email, enable, birthdate, address, marital_status
                FROM persons
            ");
    
            // Données pour la table "bank_accounts"
            $bankAccounts = $connection->fetchAllAssociative("
                SELECT id AS bank_account_id, person_id, account_number, balance
                FROM bank_accounts
            ");
    
            // Données pour la table "addresses"
            $addresses = $connection->fetchAllAssociative("
                SELECT id AS address_id, person_id, address_line, city, postal_code, country
                FROM addresses
            ");
    
            return $this->render('ex08/view_relations.html.twig', [
                'persons' => $persons,
                'bankAccounts' => $bankAccounts,
                'addresses' => $addresses,
            ]);
        } catch (DBALException $e) {
            $this->logger->error('Database error: ' . $e->getMessage());
            return new Response('❌ Database error: ' . $e->getMessage(), 500);
        }
    }
    

    #[Route('/ex08/insert-bank-account', name: 'ex08_insert_bank_account', methods: ['GET', 'POST'])]
    public function insertBankAccount(Request $request): Response
    {
        $message = '';
    
        if ($request->isMethod('POST')) {
            try {
                $connection = $this->entityManager->getConnection();
    
                // Vérifier si la personne a déjà un compte bancaire
                $sqlCheck = "SELECT COUNT(*) FROM bank_accounts WHERE person_id = :person_id";
                $existingCount = $connection->fetchOne($sqlCheck, [
                    'person_id' => $request->request->get('person_id'),
                ]);
    
                if ($existingCount > 0) {
                    $message = '❌ Cette personne a déjà un compte bancaire.';
                } else {
                    // Insérer un nouveau compte bancaire
                    $sqlInsert = "
                        INSERT INTO bank_accounts (person_id, account_number, balance)
                        VALUES (:person_id, :account_number, :balance)
                    ";
    
                    $connection->executeStatement($sqlInsert, [
                        'person_id' => $request->request->get('person_id'),
                        'account_number' => $request->request->get('account_number'),
                        'balance' => $request->request->get('balance'),
                    ]);
    
                    $message = '✅ Compte bancaire inséré avec succès !';
                }
            } catch (DBALException $e) {
                $this->logger->error('Database error: ' . $e->getMessage());
                $message = '❌ Erreur de base de données : ' . $e->getMessage();
            }
        }
    
        return $this->render('ex08/insert_bank_account.html.twig', [
            'message' => $message,
        ]);
    }     
    
    #[Route('/ex08/insert-address', name: 'ex08_insert_address', methods: ['GET', 'POST'])]
    public function insertAddress(Request $request): Response
    {
        $message = '';
    
        if ($request->isMethod('POST')) {
            try {
                $connection = $this->entityManager->getConnection();
                $sql = "
                    INSERT INTO addresses (person_id, address_line, city, postal_code, country)
                    VALUES (:person_id, :address_line, :city, :postal_code, :country)
                ";
    
                $connection->executeStatement($sql, [
                    'person_id' => $request->request->get('person_id'),
                    'address_line' => $request->request->get('address_line'),
                    'city' => $request->request->get('city'),
                    'postal_code' => $request->request->get('postal_code'),
                    'country' => $request->request->get('country'),
                ]);
    
                $message = '✅ Address inserted successfully!';
            } catch (DBALException $e) {
                $this->logger->error('Database error: ' . $e->getMessage());
                $message = '❌ Database error: ' . $e->getMessage();
            }
        }
    
        return $this->render('ex08/insert_address.html.twig', [
            'message' => $message,
        ]);
    }
    
}
