<?php

namespace App\Repository;

use App\Entity\Ex13Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Ex13EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ex13Employee::class);
    }

    /**
     * Trouve tous les employés sans manager (top level)
     */
    public function findAllTopLevel()
    {
        return $this->createQueryBuilder('e')
            ->where('e.manager IS NULL')
            ->orderBy('e.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve tous les employés actifs
     */
    public function findAllActive()
    {
        return $this->createQueryBuilder('e')
            ->where('e.active = :active')
            ->setParameter('active', true)
            ->orderBy('e.lastname', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les employés par position
     */
    public function findByPosition(string $position)
    {
        return $this->createQueryBuilder('e')
            ->where('e.position = :position')
            ->setParameter('position', $position)
            ->orderBy('e.lastname', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les employés avec leur manager et leurs subordonnés
     */
    public function findAllWithRelations()
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.manager', 'm')
            ->leftJoin('e.subordinates', 's')
            ->addSelect('m')
            ->addSelect('s')
            ->orderBy('e.lastname', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche les employés par critères multiples
     */
    public function findBySearchCriteria(array $criteria)
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.manager', 'm')
            ->addSelect('m');

        if (!empty($criteria['name'])) {
            $qb->andWhere('e.firstname LIKE :name OR e.lastname LIKE :name')
                ->setParameter('name', '%' . $criteria['name'] . '%');
        }

        if (!empty($criteria['position'])) {
            $qb->andWhere('e.position = :position')
                ->setParameter('position', $criteria['position']);
        }

        if (isset($criteria['active'])) {
            $qb->andWhere('e.active = :active')
                ->setParameter('active', $criteria['active']);
        }

        if (!empty($criteria['manager'])) {
            $qb->andWhere('m.id = :manager')
                ->setParameter('manager', $criteria['manager']);
        }

        return $qb->orderBy('e.lastname', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les employés embauchés entre deux dates
     */
    public function findByEmploymentPeriod(\DateTime $startDate, \DateTime $endDate)
    {
        return $this->createQueryBuilder('e')
            ->where('e.employedSince >= :startDate')
            ->andWhere('e.employedSince <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('e.employedSince', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les employés avec un salaire dans une fourchette donnée
     */
    public function findBySalaryRange(int $minSalary, int $maxSalary)
    {
        return $this->createQueryBuilder('e')
            ->where('e.salary >= :minSalary')
            ->andWhere('e.salary <= :maxSalary')
            ->setParameter('minSalary', $minSalary)
            ->setParameter('maxSalary', $maxSalary)
            ->orderBy('e.salary', 'DESC')
            ->getQuery()
            ->getResult();
    }
}