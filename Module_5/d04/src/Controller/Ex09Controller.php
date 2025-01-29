<?php

namespace App\Controller;

use App\Entity\Ex09Person;
use App\Entity\Ex09BankAccount;
use App\Entity\Ex09Address;
use App\Form\Ex09PersonType;
use App\Form\Ex09BankAccountType;
use App\Form\Ex09AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Ex09Controller extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/ex09/create-table', name: 'ex09_create_table')]
    public function createTable(): Response
    {
        $message = '';
        try {
            $connection = $this->entityManager->getConnection();
    
            $sql = "
                CREATE TABLE IF NOT EXISTS ex09_person (
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
    
            $message = '✅ Table "ex09_person" créée avec succès !';
        } catch (\Exception $e) {
            $message = '❌ Erreur lors de la création de la table : ' . $e->getMessage();
        }
    
        return $this->render('ex09/create_table.html.twig', [
            'message' => $message,
        ]);
    }    
    
    #[Route('/ex09/create', name: 'ex09_create')]
    public function create(Request $request): Response
    {
        $schemaManager = $this->entityManager->getConnection()->createSchemaManager();
        if (!$schemaManager->tablesExist(['ex09_person'])) {
            $this->addFlash('error', '❌ La table "ex09_person" n\'existe pas. Veuillez la créer avant d\'ajouter des données.');
            return $this->redirectToRoute('ex09_create_table');
        }
    
        $person = new Ex09Person();
        $form = $this->createForm(Ex09PersonType::class, $person);
        $form->handleRequest($request);
    
        // Message flash défini uniquement si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($person);
            $this->entityManager->flush();
    
            $this->addFlash('success', '✅ Personne ajoutée avec succès !');
            return $this->redirectToRoute('ex09_create');
        }
    
        return $this->render('ex09/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }    

    #[Route('/ex09/view', name: 'ex09_view')]
    public function view(): Response
    {
        $repository = $this->entityManager->getRepository(Ex09Person::class);
        $persons = $repository->findAll();
    
        return $this->render('ex09/view.html.twig', [
            'persons' => $persons,
        ]);
    }
    
    #[Route('/ex09/delete/{id}', name: 'ex09_delete')]
    public function delete(int $id): Response
    {
        $repository = $this->entityManager->getRepository(Ex09Person::class);
        $person = $repository->find($id);

        if (!$person) {
            $this->addFlash('error', '❌ Personne introuvable.');
        } else {
            $this->entityManager->remove($person);
            $this->entityManager->flush();
            $this->addFlash('success', '✅ Personne supprimée avec succès !');
        }

        return $this->redirectToRoute('ex09_view');
    }

    #[Route('/ex09/edit/{id}', name: 'ex09_edit')]
    public function edit(Request $request, int $id): Response
    {
        $repository = $this->entityManager->getRepository(Ex09Person::class);
        $person = $repository->find($id);
    
        if (!$person) {
            $this->addFlash('error', '❌ Personne introuvable.');
            return $this->redirectToRoute('ex09_view');
        }
    
        $form = $this->createForm(Ex09PersonType::class, $person);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
    
            $this->addFlash('success', '✅ Personne mise à jour avec succès !');
            return $this->redirectToRoute('ex09_view');
        }
    
        return $this->render('ex09/edit.html.twig', [
            'form' => $form->createView(),
            'person' => $person,
        ]);
    }
    
    #[Route('/ex09/add-bank-account', name: 'ex09_add_bank_account', methods: ['GET', 'POST'])]
    public function addBankAccount(Request $request): Response
    {
        $bankAccount = new Ex09BankAccount();
        $form = $this->createForm(Ex09BankAccountType::class, $bankAccount);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $person = $bankAccount->getPerson();
    
            if ($person->getBankAccount()) {
                // Ce cas ne devrait pas se produire grâce au filtre, mais reste une sécurité
                $this->addFlash('error', '❌ Cette personne a déjà un compte bancaire.');
            } else {
                $this->entityManager->persist($bankAccount);
                $this->entityManager->flush();
    
                $this->addFlash('success', '✅ Compte bancaire ajouté avec succès !');
                return $this->redirectToRoute('ex09_view');
            }
        }
    
        return $this->render('ex09/add_bank_account.html.twig', [
            'form' => $form->createView(),
        ]);
    }    
    
    #[Route('/ex09/add-address', name: 'ex09_add_address', methods: ['GET', 'POST'])]
    public function addAddress(Request $request): Response
    {
        $address = new Ex09Address();
        $form = $this->createForm(Ex09AddressType::class, $address);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération de l'ID de la personne depuis le formulaire
            $personId = $form->get('person')->getData();
            $person = $this->entityManager->getRepository(Ex09Person::class)->find($personId);
    
            if (!$person) {
                $this->addFlash('error', '❌ Aucune personne trouvée avec cet ID.');
            } else {
                // Vérification si l'adresse existe déjà
                $existingAddress = $this->entityManager->getRepository(Ex09Address::class)->findOneBy([
                    'addressLine' => $address->getAddressLine(),
                    'city' => $address->getCity(),
                    'postalCode' => $address->getPostalCode(),
                    'country' => $address->getCountry(),
                    'person' => $person,
                ]);
    
                if ($existingAddress) {
                    $this->addFlash('error', '❌ Cette adresse existe déjà pour cette personne.');
                } else {
                    $address->setPerson($person);
                    $this->entityManager->persist($address);
                    $this->entityManager->flush();
    
                    $this->addFlash('success', '✅ Adresse ajoutée avec succès !');
                    return $this->redirectToRoute('ex09_view');
                }
            }
        }
    
        return $this->render('ex09/add_address.html.twig', [
            'form' => $form->createView(),
        ]);
    }        

    #[Route('/ex09/view-relations', name: 'ex09_view_relations')]
    public function viewRelations(): Response
    {
        $persons = $this->entityManager->getRepository(Ex09Person::class)->findAll();
        $bankAccounts = $this->entityManager->getRepository(Ex09BankAccount::class)->findAll();
        $addresses = $this->entityManager->getRepository(Ex09Address::class)->findAll();
    
        return $this->render('ex09/view_relations.html.twig', [
            'persons' => $persons,
            'bankAccounts' => $bankAccounts,
            'addresses' => $addresses,
        ]);
    }
    
}
