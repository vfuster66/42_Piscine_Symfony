<?php

namespace App\Controller;

use App\Entity\Ex03Person;
use App\Form\Ex03PersonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Ex03Controller extends AbstractController
{

    #[Route('/ex03/create-table', name: 'ex03_create_table')]
    public function createTable(EntityManagerInterface $entityManager): Response
    {
        $message = '';
        try {
            $connection = $entityManager->getConnection();
    
            $sql = "
                CREATE TABLE IF NOT EXISTS ex03_person (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(255) UNIQUE NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    enable BOOLEAN NOT NULL,
                    birthdate DATETIME NOT NULL,
                    address TEXT NOT NULL
                )
            ";
    
            $connection->executeStatement($sql);
    
            $message = '✅ Table "ex03_person" créée avec succès !';
        } catch (\Exception $e) {
            $message = '❌ Erreur lors de la création de la table : ' . $e->getMessage();
        }
    
        return $this->render('ex03/create_table.html.twig', [
            'message' => $message,
        ]);
    }    
    
    #[Route('/ex03/create', name: 'ex03_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $schemaManager = $entityManager->getConnection()->createSchemaManager();
        if (!$schemaManager->tablesExist(['ex03_person'])) {
            $this->addFlash('error', '❌ La table "ex03_person" n\'existe pas. Veuillez la créer avant d\'ajouter des données.');
            return $this->redirectToRoute('ex03_create_table');
        }
    
        $person = new Ex03Person();
        $form = $this->createForm(Ex03PersonType::class, $person);
        $form->handleRequest($request);
    
        // Message flash défini uniquement si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($person);
            $entityManager->flush();
    
            $this->addFlash('success', '✅ Personne ajoutée avec succès !');
            return $this->redirectToRoute('ex03_create'); // Redirection pour éviter un double envoi
        }
    
        return $this->render('ex03/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }    

    #[Route('/ex03/view', name: 'ex03_view')]
    public function view(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Ex03Person::class);
        $persons = $repository->findAll();
    
        return $this->render('ex03/view.html.twig', [
            'persons' => $persons,
        ]);
    }
    
}
