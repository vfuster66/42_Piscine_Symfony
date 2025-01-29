<?php
// src/Repository/PostRepository.php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    // Vous pouvez ajouter des méthodes personnalisées ici
    // Par exemple :
    public function findLatestPosts(int $limit = 10)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.created', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}