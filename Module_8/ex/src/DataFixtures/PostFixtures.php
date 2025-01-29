<?php
// src/DataFixtures/PostFixtures.php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $post = new Post();
            $post->setTitle('Post ' . $i);
            $post->setContent('Contenu du post ' . $i);
            $manager->persist($post);
        }

        $manager->flush();
    }
}