<?php
// src/Controller/PostController.php
namespace App\Controller;

use App\Entity\Post;
use App\Entity\Vote;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/posts', name: 'post_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $posts = $entityManager->getRepository(Post::class)->findBy([], ['created' => 'DESC']);
        
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/new', name: 'post_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        if (!$user->canCreatePost()) {
            $this->addFlash('error', 'You do not have permission to create a post.');
            return $this->redirectToRoute('post_index');
        }

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($user);

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}', name: 'post_show')]
    public function show(Post $post): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'likesCount' => $post->getLikesCount(),
            'dislikesCount' => $post->getDislikesCount(),
            'userVote' => $user ? $user->hasVotedOnPost($post) : null,
        ]);
    }

    #[Route('/post/{id}/edit', name: 'post_edit')]
    public function edit(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
    
        if (!$user->canEditAnyPost() && $user !== $post->getAuthor()) {
            throw $this->createAccessDeniedException("You do not have permission to edit this post.");
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUpdatedAt(new \DateTime());
            $post->setLastEditor($user);

            $entityManager->flush();

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post
        ]);
    }

    #[Route('/post/{id}/vote/{type}', name: 'post_vote')]
    public function vote(Post $post, string $type, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (($type === 'like' && !$user->canLikePost()) || ($type === 'dislike' && !$user->canDislikePost())) {
            $this->addFlash('error', 'You do not have permission to vote.');
            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        $existingVote = $entityManager->getRepository(Vote::class)->findVoteByUserAndPost($user, $post);

        if ($existingVote) {
            if (($type === 'like' && $existingVote->isLike()) || ($type === 'dislike' && !$existingVote->isLike())) {
                $entityManager->remove($existingVote);
                $this->addFlash('success', 'Vote removed.');
            } else {
                $existingVote->setIsLike($type === 'like');
                $this->addFlash('success', 'Vote updated.');
            }
        } else {
            $vote = new Vote();
            $vote->setPost($post)
                 ->setUser($user)
                 ->setIsLike($type === 'like');

            $entityManager->persist($vote);
            $this->addFlash('success', 'Vote added.');
        }

        $entityManager->flush();
        return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
    }
}
