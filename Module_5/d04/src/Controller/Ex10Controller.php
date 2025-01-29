<?php

namespace App\Controller;

use App\Entity\Ex10Data;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Ex10Controller extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/ex10/create-table', name: 'ex10_create_table')]
    public function createTable(): Response
    {
        try {
            $connection = $this->entityManager->getConnection();
    
            $sql = "
                CREATE TABLE IF NOT EXISTS ex10_data (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    field1 VARCHAR(255) NOT NULL,
                    field2 VARCHAR(255) NOT NULL,
                    field3 DATETIME NOT NULL
                );
            ";
    
            $connection->executeStatement($sql);
    
            $message = '✅ Table "ex10_data" créée avec succès.';
        } catch (\Exception $e) {
            $message = '❌ Erreur lors de la création de la table : ' . $e->getMessage();
        }
    
        return $this->render('ex10/create_table.html.twig', [
            'message' => $message,
        ]);
    }    

    #[Route('/ex10/process-file', name: 'ex10_process_file')]
    public function processFile(): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/data/ex10_data.csv';
    
        if (!file_exists($filePath)) {
            return new Response('❌ Fichier non trouvé.', Response::HTTP_BAD_REQUEST);
        }
    
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return new Response('❌ Impossible de lire le fichier.', Response::HTTP_BAD_REQUEST);
        }
    
        // Lire le contenu du fichier
        $header = fgetcsv($handle);
        while (($data = fgetcsv($handle)) !== false) {
            [$field1, $field2, $field3] = $data;
    
            // Vérification des doublons avec ORM
            $existingData = $this->entityManager->getRepository(Ex10Data::class)->findOneBy([
                'field1' => $field1,
                'field2' => $field2,
                'field3' => new \DateTime($field3),
            ]);
    
            if ($existingData === null) {
                // Insertion avec SQL
                $connection = $this->entityManager->getConnection();
                $connection->insert('ex10_data', [
                    'field1' => $field1,
                    'field2' => $field2,
                    'field3' => $field3,
                ]);
    
                // Insertion avec ORM
                $entity = new Ex10Data();
                $entity->setField1($field1);
                $entity->setField2($field2);
                $entity->setField3(new \DateTime($field3));
                $this->entityManager->persist($entity);
            }
        }
    
        fclose($handle);
    
        $this->entityManager->flush();
    
        return $this->redirectToRoute('ex10_view_data');
    }    

    #[Route('/ex10/view', name: 'ex10_view_data')]
    public function viewData(): Response
    {
        $repository = $this->entityManager->getRepository(Ex10Data::class);
        $data = $repository->findAll();

        return $this->render('ex10/view.html.twig', ['data' => $data]);
    }
}
