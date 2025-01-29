<?php
// src/Controller/VoteController.php
namespace App\Controller;

use App\Entity\Post;
use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoteController extends AbstractController
{
    #[Route('/post/{id}/vote/{type}', name: 'post_vote')]
    public function vote(Post $post, string $type, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($type === 'like' && !$user->canLikePost()) {
            $this->addFlash('error', 'You need at least 3 reputation points to like a post.');
            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        if ($type === 'dislike' && !$user->canDislikePost()) {
            $this->addFlash('error', 'You need at least 6 reputation points to dislike a post.');
            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        $existingVote = $entityManager->getRepository(Vote::class)->findVoteByUserAndPost($user, $post);

        if ($existingVote) {

            if (($type === 'like' && $existingVote->isLike()) || ($type === 'dislike' && !$existingVote->isLike())) {
                $entityManager->remove($existingVote);
                $this->addFlash('success', 'Your vote has been removed.');
            } else {

                $existingVote->setIsLike($type === 'like');
                $this->addFlash('success', 'Your vote has been updated.');
            }
        } else {

            $vote = new Vote();
            $vote->setPost($post)
                 ->setUser($user)
                 ->setIsLike($type === 'like');

            $entityManager->persist($vote);
            $this->addFlash('success', 'Your vote has been added.');
        }

        $entityManager->flush();

        return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
    }
}
