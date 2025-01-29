<?php

namespace App\Controller;

use App\Entity\Ex07Person;
use App\Form\Ex07PersonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Ex07Controller extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/ex07/create-table', name: 'ex07_create_table')]
    public function createTable(): Response
    {
        $message = '';
        try {
            $connection = $this->entityManager->getConnection();
    
            $sql = "
                CREATE TABLE IF NOT EXISTS ex07_person (
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
    
            $message = '✅ Table "ex07_person" créée avec succès !';
        } catch (\Exception $e) {
            $message = '❌ Erreur lors de la création de la table : ' . $e->getMessage();
        }
    
        return $this->render('ex07/create_table.html.twig', [
            'message' => $message,
        ]);
    }    
    
    #[Route('/ex07/create', name: 'ex07_create')]
    public function create(Request $request): Response
    {
        $schemaManager = $this->entityManager->getConnection()->createSchemaManager();
        if (!$schemaManager->tablesExist(['ex07_person'])) {
            $this->addFlash('error', '❌ La table "ex07_person" n\'existe pas. Veuillez la créer avant d\'ajouter des données.');
            return $this->redirectToRoute('ex07_create_table');
        }
    
        $person = new Ex07Person();
        $form = $this->createForm(Ex07PersonType::class, $person);
        $form->handleRequest($request);
    
        // Message flash défini uniquement si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($person);
            $this->entityManager->flush();
    
            $this->addFlash('success', '✅ Personne ajoutée avec succès !');
            return $this->redirectToRoute('ex07_create'); // Redirection pour éviter un double envoi
        }
    
        return $this->render('ex07/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }    

    #[Route('/ex07/view', name: 'ex07_view')]
    public function view(): Response
    {
        $repository = $this->entityManager->getRepository(Ex07Person::class);
        $persons = $repository->findAll();
    
        return $this->render('ex07/view.html.twig', [
            'persons' => $persons,
        ]);
    }
    
    #[Route('/ex07/delete/{id}', name: 'ex07_delete')]
    public function delete(int $id): Response
    {
        $repository = $this->entityManager->getRepository(Ex07Person::class);
        $person = $repository->find($id);

        if (!$person) {
            $this->addFlash('error', '❌ Personne introuvable.');
        } else {
            $this->entityManager->remove($person);
            $this->entityManager->flush();
            $this->addFlash('success', '✅ Personne supprimée avec succès !');
        }

        return $this->redirectToRoute('ex07_view');
    }

    #[Route('/ex07/edit/{id}', name: 'ex07_edit')]
    public function edit(Request $request, int $id): Response
    {
        $repository = $this->entityManager->getRepository(Ex07Person::class);
        $person = $repository->find($id);
    
        if (!$person) {
            $this->addFlash('error', '❌ Personne introuvable.');
            return $this->redirectToRoute('ex07_view');
        }
    
        $form = $this->createForm(Ex07PersonType::class, $person);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
    
            $this->addFlash('success', '✅ Personne mise à jour avec succès !');
            return $this->redirectToRoute('ex07_view');
        }
    
        return $this->render('ex07/edit.html.twig', [
            'form' => $form->createView(),
            'person' => $person,
        ]);
    }
    
}
