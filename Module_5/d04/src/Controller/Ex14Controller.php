<?php
// src/Controller/Ex14Controller.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class Ex14Controller extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/ex14/create-table', name: 'ex14_create_table')]
    public function createTable(): Response
    {
        $message = '';
        try {
            $connection = $this->entityManager->getConnection();
            
            // Version vulnérable de la table pour la démonstration
            $sql = "
                CREATE TABLE IF NOT EXISTS ex14_users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(255) NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    is_admin BOOLEAN DEFAULT FALSE
                )
            ";
            
            $connection->executeStatement($sql);
            
            // Insérer des données de test
            $sql = "
                INSERT INTO ex14_users (username, password, is_admin) 
                VALUES 
                ('admin', 'admin123', TRUE),
                ('user1', 'pass123', FALSE)
                ON DUPLICATE KEY UPDATE username=username
            ";
            
            $connection->executeStatement($sql);
            
            $message = '✅ Table "ex14_users" créée avec succès !';
        } catch (\Exception $e) {
            $message = '❌ Erreur lors de la création de la table : ' . $e->getMessage();
        }

        return $this->render('ex14/create_table.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/ex14/login', name: 'ex14_login')]
    public function login(Request $request): Response
    {
        $error = null;
        $success = null;
        $debugSQL = null; // Pour afficher la requête SQL
    
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');
    
            try {
                $connection = $this->entityManager->getConnection();
                
                // Construction de la requête SQL vulnérable
                $sql = "SELECT * FROM ex14_users WHERE username = '$username' AND password = '$password'";
                
                // Sauvegarde de la requête pour le debug
                $debugSQL = "Requête exécutée : " . $sql;
                
                try {
                    $result = $connection->fetchAssociative($sql);
                    
                    if ($result) {
                        $success = "✅ Connexion réussie ! Bienvenue " . $result['username'];
                        if ($result['is_admin']) {
                            $success .= " (Administrateur)";
                        }
                    } else {
                        $error = "❌ Identifiants invalides";
                    }
                } catch (\Exception $e) {
                    // Si une requête de type DROP TABLE est exécutée
                    if (stripos($e->getMessage(), 'DROP') !== false) {
                        $error = "⚠️ La table a été supprimée !";
                    } else {
                        $error = "❌ Erreur SQL : " . $e->getMessage();
                    }
                }
            } catch (\Exception $e) {
                $error = "❌ Erreur de connexion à la base de données";
            }
        }
    
        return $this->render('ex14/login.html.twig', [
            'error' => $error,
            'success' => $success,
            'debugSQL' => $debugSQL
        ]);
    }

    #[Route('/ex14/view', name: 'ex14_view')]
    public function view(): Response
    {
        $users = [];
        try {
            $connection = $this->entityManager->getConnection();
            $users = $connection->fetchAllAssociative('SELECT * FROM ex14_users');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des utilisateurs');
        }

        return $this->render('ex14/view.html.twig', [
            'users' => $users
        ]);
    }
}