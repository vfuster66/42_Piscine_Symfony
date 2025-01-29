<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Post;
use App\Entity\Vote;
use App\Entity\Admin;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];
        $posts = [];

        $admin = new Admin();
        $admin->setUsername("admin")
            ->setEmail("admin@example.com")
            ->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'))
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);


        // Définition des niveaux de réputation contrôlés
        $reputationLevels = [0, 3, 6, 9, 12]; 

        // Création d'utilisateurs avec ces niveaux de réputation
        foreach ($reputationLevels as $index => $points) {
            $user = new User();
            $user->setUsername("user" . ($index + 1))
                 ->setEmail("user" . ($index + 1) . "@example.com")
                 ->setPassword($this->passwordHasher->hashPassword($user, 'password'))
                 ->setRoles(['ROLE_USER']); // Chaque utilisateur est un simple utilisateur

            $manager->persist($user);
            $users[] = $user;
        }

        // Création de 10 posts associés aux utilisateurs aléatoires
        for ($i = 1; $i <= 10; $i++) {
            $post = new Post();
            $post->setTitle("Post $i")
                 ->setContent("This is the content of post $i.")
                 ->setAuthor($users[array_rand($users)]); 

            $manager->persist($post);
            $posts[] = $post;
        }

        // Attribution de votes contrôlés pour ajuster la réputation
        foreach ($users as $index => $user) {
            $votesToGive = $reputationLevels[$index]; // Récupération du nombre de votes à donner

            for ($i = 0; $i < $votesToGive; $i++) {
                $vote = new Vote();
                $vote->setUser($user)
                     ->setPost($posts[array_rand($posts)]) // Vote sur un post aléatoire
                     ->setIsLike(true); // On met uniquement des votes positifs pour influencer la réputation

                $manager->persist($vote);
            }
        }

        $manager->flush();
    }
}
