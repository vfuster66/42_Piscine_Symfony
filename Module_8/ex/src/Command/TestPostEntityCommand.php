<?php
// src/Command/TestPostEntityCommand.php

namespace App\Command;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestPostEntityCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:test-post')
            ->setDescription('Test the Post entity');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            // Création d'un nouveau post
            $post = new Post();
            $post->setTitle('Post de test');
            $post->setContent('Ceci est un contenu de test');

            // Persistance en base de données
            $this->entityManager->persist($post);
            $this->entityManager->flush();

            $io->success([
                'Post créé avec succès !',
                'ID: ' . $post->getId(),
                'Titre: ' . $post->getTitle(),
                'Date de création: ' . $post->getCreated()->format('Y-m-d H:i:s')
            ]);

            // Récupération pour vérification
            $found = $this->entityManager->getRepository(Post::class)->find($post->getId());
            if ($found) {
                $io->info('Post retrouvé en base de données');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erreur: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}