<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\User;
use App\Form\AdminType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
   #[Route('/', name: 'admin_dashboard')]
   public function dashboard(): Response
   {
       return $this->render('admin/dashboard.html.twig');
   }

   #[Route('/users', name: 'admin_users')]
   public function usersList(EntityManagerInterface $entityManager): Response
   {
       $userRepository = $entityManager->getRepository(User::class);
       
       // Récupérer tous les utilisateurs réguliers
       $regularUsers = $userRepository->createQueryBuilder('u')
           ->where('u.roles LIKE :role')
           ->setParameter('role', '%"ROLE_USER"%')
           ->andWhere('u.roles NOT LIKE :adminRole')
           ->setParameter('adminRole', '%"ROLE_ADMIN"%')
           ->getQuery()
           ->getResult();
   
       // Récupérer tous les admins
       $admins = $userRepository->createQueryBuilder('u')
           ->where('u.roles LIKE :role')
           ->setParameter('role', '%"ROLE_ADMIN"%')
           ->getQuery()
           ->getResult();
   
       return $this->render('admin/users.html.twig', [
           'users' => $regularUsers,
           'admins' => $admins
       ]);
   }

   #[Route('/new', name: 'admin_new')]
   public function new(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
   {
       $admin = new Admin();
       $form = $this->createForm(AdminType::class, $admin);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
           $admin->setPassword(
               $passwordHasher->hashPassword(
                   $admin,
                   $form->get('plainPassword')->getData()
               )
           );
           $admin->setLastLoginAt(new \DateTime());

           $entityManager->persist($admin);
           $entityManager->flush();

           $this->addFlash('success', 'Admin created successfully');
           return $this->redirectToRoute('admin_users');
       }

       return $this->render('admin/new.html.twig', [
           'form' => $form->createView()
       ]);
   }

   #[Route('/delete/{id}', name: 'admin_user_delete')]
   public function delete(User $user, EntityManagerInterface $entityManager): Response
   {
       if ($user === $this->getUser()) {
           $this->addFlash('error', 'You cannot delete your own account');
           return $this->redirectToRoute('admin_users');
       }

       $entityManager->remove($user);
       $entityManager->flush();

       $this->addFlash('success', 'User deleted successfully');
       return $this->redirectToRoute('admin_users');
   }
}