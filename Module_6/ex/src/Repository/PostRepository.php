<?php
// src/Repository/PostRepository.php
namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Save a Post entity.
     *
     * @param Post $entity
     * @param bool $flush
     */
    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove a Post entity.
     *
     * @param Post $entity
     * @param bool $flush
     */
    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find posts ordered by creation date (newest first).
     *
     * @return Post[]
     */
    public function findAllOrderedByCreated(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.created', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPaginated(int $page, int $limit = 10): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->orderBy('p.created', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit + 1); // Charger un élément supplémentaire pour vérifier s'il reste des pages
    
        $results = $queryBuilder->getQuery()->getResult();
    
        return [
            'items' => array_slice($results, 0, $limit), // Limiter les éléments retournés à la taille réelle
            'hasNext' => count($results) > $limit, // Si on a récupéré plus que le `limit`, il reste des pages
        ];
    }    

}
