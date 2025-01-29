<?php

namespace App\Controller;

use App\Entity\Ex05Person;
use App\Form\Ex05PersonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Ex05Controller extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/ex05/create-table', name: 'ex05_create_table')]
    public function createTable(): Response
    {
        $message = '';
        try {
            $connection = $this->entityManager->getConnection();
    
            $sql = "
                CREATE TABLE IF NOT EXISTS ex05_person (
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
    
            $message = '✅ Table "ex05_person" créée avec succès !';
        } catch (\Exception $e) {
            $message = '❌ Erreur lors de la création de la table : ' . $e->getMessage();
        }
    
        return $this->render('ex05/create_table.html.twig', [
            'message' => $message,
        ]);
    }    
    
    #[Route('/ex05/create', name: 'ex05_create')]
    public function create(Request $request): Response
    {
        $schemaManager = $this->entityManager->getConnection()->createSchemaManager();
        if (!$schemaManager->tablesExist(['ex05_person'])) {
            $this->addFlash('error', '❌ La table "ex05_person" n\'existe pas. Veuillez la créer avant d\'ajouter des données.');
            return $this->redirectToRoute('ex05_create_table');
        }
    
        $person = new Ex05Person();
        $form = $this->createForm(Ex05PersonType::class, $person);
        $form->handleRequest($request);
    
        // Message flash défini uniquement si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($person);
            $this->entityManager->flush();
    
            $this->addFlash('success', '✅ Personne ajoutée avec succès !');
            return $this->redirectToRoute('ex05_create'); // Redirection pour éviter un double envoi
        }
    
        return $this->render('ex05/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }    

    #[Route('/ex05/view', name: 'ex05_view')]
    public function view(): Response
    {
        $repository = $this->entityManager->getRepository(Ex05Person::class);
        $persons = $repository->findAll();
    
        return $this->render('ex05/view.html.twig', [
            'persons' => $persons,
        ]);
    }
    
    #[Route('/ex05/delete/{id}', name: 'ex05_delete')]
    public function delete(int $id): Response
    {
        $repository = $this->entityManager->getRepository(Ex05Person::class);
        $person = $repository->find($id);

        if (!$person) {
            $this->addFlash('error', '❌ Personne introuvable.');
        } else {
            $this->entityManager->remove($person);
            $this->entityManager->flush();
            $this->addFlash('success', '✅ Personne supprimée avec succès !');
        }

        return $this->redirectToRoute('ex05_view');
    }
}
