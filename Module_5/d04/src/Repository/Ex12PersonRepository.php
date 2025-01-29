<?php

namespace App\Repository;

use App\Entity\Ex12Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Ex12PersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ex12Person::class);
    }

    public function findWithFiltersAndSorting(?string $name, ?string $city, string $sort, string $order)
    {
        $allowedSorts = ['name', 'email', 'birthdate'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'name';
        }

        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.addresses', 'a')
            ->addSelect('a')
            ->leftJoin('p.bankAccount', 'b')
            ->addSelect('b');

        if ($name) {
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $name . '%');
        }

        if ($city) {
            $qb->andWhere('a.city LIKE :city')
                ->setParameter('city', '%' . $city . '%');
        }

        $qb->orderBy('p.' . $sort, $order);

        return $qb->getQuery()->getResult();
    }
}