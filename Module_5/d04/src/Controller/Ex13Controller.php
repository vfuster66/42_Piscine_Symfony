<?php
// src/Controller/Ex13Controller.php

namespace App\Controller;

use App\Entity\Ex13Employee;
use App\Form\Ex13EmployeeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Ex13Controller extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/ex13/create', name: 'ex13_create')]
    public function create(Request $request): Response
    {
        $employee = new Ex13Employee();
        $form = $this->createForm(Ex13EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($employee);
                $this->entityManager->flush();
                $this->addFlash('success', '✅ Employé ajouté avec succès !');
                return $this->redirectToRoute('ex13_list');
            } catch (\Exception $e) {
                $this->addFlash('error', '❌ Erreur lors de l\'ajout : ' . $e->getMessage());
            }
        }

        return $this->render('ex13/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ex13/list', name: 'ex13_list')]
    public function list(): Response
    {
        $employees = $this->entityManager->getRepository(Ex13Employee::class)->findAll();
        
        return $this->render('ex13/list.html.twig', [
            'employees' => $employees,
        ]);
    }

    #[Route('/ex13/edit/{id}', name: 'ex13_edit')]
    public function edit(Request $request, int $id): Response
    {
        $employee = $this->entityManager->getRepository(Ex13Employee::class)->find($id);
        
        if (!$employee) {
            $this->addFlash('error', '❌ Employé non trouvé.');
            return $this->redirectToRoute('ex13_list');
        }

        $form = $this->createForm(Ex13EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->flush();
                $this->addFlash('success', '✅ Employé mis à jour avec succès !');
                return $this->redirectToRoute('ex13_list');
            } catch (\Exception $e) {
                $this->addFlash('error', '❌ Erreur lors de la mise à jour : ' . $e->getMessage());
            }
        }

        return $this->render('ex13/edit.html.twig', [
            'form' => $form->createView(),
            'employee' => $employee,
        ]);
    }

    #[Route('/ex13/delete/{id}', name: 'ex13_delete')]
    public function delete(int $id): Response
    {
        $employee = $this->entityManager->getRepository(Ex13Employee::class)->find($id);

        if (!$employee) {
            $this->addFlash('error', '❌ Employé non trouvé.');
            return $this->redirectToRoute('ex13_list');
        }

        try {
            $this->entityManager->remove($employee);
            $this->entityManager->flush();
            $this->addFlash('success', '✅ Employé supprimé avec succès !');
        } catch (\Exception $e) {
            $this->addFlash('error', '❌ Erreur lors de la suppression : ' . $e->getMessage());
        }

        return $this->redirectToRoute('ex13_list');
    }

    #[Route('/ex13/details/{id}', name: 'ex13_details')]
    public function details(int $id): Response
    {
        $employee = $this->entityManager->getRepository(Ex13Employee::class)->find($id);

        if (!$employee) {
            $this->addFlash('error', '❌ Employé non trouvé.');
            return $this->redirectToRoute('ex13_list');
        }

        return $this->render('ex13/details.html.twig', [
            'employee' => $employee,
        ]);
    }
}