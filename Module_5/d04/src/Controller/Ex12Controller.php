<?php

namespace App\Controller;

use App\Entity\Ex12Person;
use App\Entity\Ex12BankAccount;
use App\Entity\Ex12Address;
use App\Form\Ex12PersonType;
use App\Form\Ex12BankAccountType;
use App\Form\Ex12AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Ex12Controller extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/ex12/create-table', name: 'ex12_create_table')]
    public function createTable(): Response
    {
        $message = '';
        try {
            $connection = $this->entityManager->getConnection();
    
            $sql = "
                CREATE TABLE IF NOT EXISTS ex12_person (
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
    
            $message = '✅ Table "ex12_person" créée avec succès !';
        } catch (\Exception $e) {
            $message = '❌ Erreur lors de la création de la table : ' . $e->getMessage();
        }
    
        return $this->render('ex12/create_table.html.twig', [
            'message' => $message,
        ]);
    }    
    
    #[Route('/ex12/create', name: 'ex12_create')]
    public function create(Request $request): Response
    {
        $schemaManager = $this->entityManager->getConnection()->createSchemaManager();
        if (!$schemaManager->tablesExist(['ex12_person'])) {
            $this->addFlash('error', '❌ La table "ex12_person" n\'existe pas. Veuillez la créer avant d\'ajouter des données.');
            return $this->redirectToRoute('ex12_create_table');
        }
    
        $person = new Ex12Person();
        $form = $this->createForm(Ex12PersonType::class, $person);
        $form->handleRequest($request);
    
        // Message flash défini uniquement si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($person);
            $this->entityManager->flush();
    
            $this->addFlash('success', '✅ Personne ajoutée avec succès !');
            return $this->redirectToRoute('ex12_create');
        }
    
        return $this->render('ex12/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }    

    #[Route('/ex12/view', name: 'ex12_view')]
    public function view(): Response
    {
        $repository = $this->entityManager->getRepository(Ex12Person::class);
        $persons = $repository->findAll();
    
        return $this->render('ex12/view.html.twig', [
            'persons' => $persons,
        ]);
    }
    
    #[Route('/ex12/delete/{id}', name: 'ex12_delete')]
    public function delete(int $id): Response
    {
        $repository = $this->entityManager->getRepository(Ex12Person::class);
        $person = $repository->find($id);

        if (!$person) {
            $this->addFlash('error', '❌ Personne introuvable.');
        } else {
            $this->entityManager->remove($person);
            $this->entityManager->flush();
            $this->addFlash('success', '✅ Personne supprimée avec succès !');
        }

        return $this->redirectToRoute('ex12_view');
    }

    #[Route('/ex12/edit/{id}', name: 'ex12_edit')]
    public function edit(Request $request, int $id): Response
    {
        $repository = $this->entityManager->getRepository(Ex12Person::class);
        $person = $repository->find($id);
    
        if (!$person) {
            $this->addFlash('error', '❌ Personne introuvable.');
            return $this->redirectToRoute('ex12_view');
        }
    
        $form = $this->createForm(Ex12PersonType::class, $person);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
    
            $this->addFlash('success', '✅ Personne mise à jour avec succès !');
            return $this->redirectToRoute('ex12_view');
        }
    
        return $this->render('ex12/edit.html.twig', [
            'form' => $form->createView(),
            'person' => $person,
        ]);
    }
    
    #[Route('/ex12/add-bank-account', name: 'ex12_add_bank_account', methods: ['GET', 'POST'])]
    public function addBankAccount(Request $request): Response
    {
        $bankAccount = new Ex12BankAccount();
        $form = $this->createForm(Ex12BankAccountType::class, $bankAccount);
    
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
                return $this->redirectToRoute('ex12_view');
            }
        }
    
        return $this->render('ex12/add_bank_account.html.twig', [
            'form' => $form->createView(),
        ]);
    }    
    
    #[Route('/ex12/add-address', name: 'ex12_add_address', methods: ['GET', 'POST'])]
    public function addAddress(Request $request): Response
    {
        $address = new Ex12Address();
        $form = $this->createForm(Ex12AddressType::class, $address);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération de l'ID de la personne depuis le formulaire
            $personId = $form->get('person')->getData();
            $person = $this->entityManager->getRepository(Ex12Person::class)->find($personId);
    
            if (!$person) {
                $this->addFlash('error', '❌ Aucune personne trouvée avec cet ID.');
            } else {
                // Vérification si l'adresse existe déjà
                $existingAddress = $this->entityManager->getRepository(Ex12Address::class)->findOneBy([
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
                    return $this->redirectToRoute('ex12_view');
                }
            }
        }
    
        return $this->render('ex12/add_address.html.twig', [
            'form' => $form->createView(),
        ]);
    }        

    #[Route('/ex12/view-relations', name: 'ex12_view_relations')]
    public function viewRelations(): Response
    {
        $persons = $this->entityManager->getRepository(Ex12Person::class)->findAll();
        $bankAccounts = $this->entityManager->getRepository(Ex12BankAccount::class)->findAll();
        $addresses = $this->entityManager->getRepository(Ex12Address::class)->findAll();
    
        return $this->render('ex12/view_relations.html.twig', [
            'persons' => $persons,
            'bankAccounts' => $bankAccounts,
            'addresses' => $addresses,
        ]);
    }
    
    #[Route('/ex12/view-filtered', name: 'ex12_view_filtered', methods: ['GET'])]
    public function viewFiltered(Request $request): Response
    {
        $name = $request->query->get('name');
        $city = $request->query->get('city');
        $sort = $request->query->get('sort', 'name');
        $order = $request->query->get('order', 'ASC');
    
        // Validation des paramètres
        if (!in_array($sort, ['name', 'email', 'birthdate'])) {
            $sort = 'name';
        }
    
        if (!in_array($order, ['ASC', 'DESC'])) {
            $order = 'ASC';
        }
    
        // Requête personnalisée avec le repository
        $repository = $this->entityManager->getRepository(Ex12Person::class);
        $data = $repository->findWithFiltersAndSorting($name, $city, $sort, $order);
    
        return $this->render('ex12/view_filtered.html.twig', [
            'data' => $data,
        ]);
    }
}
