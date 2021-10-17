<?php

namespace App\Repository\Budget;

use App\Entity\Budget\BuExpense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BuExpense|null find($id, $lockMode = null, $lockVersion = null)
 * @method BuExpense|null findOneBy(array $criteria, array $orderBy = null)
 * @method BuExpense[]    findAll()
 * @method BuExpense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuExpense::class);
    }

    // /**
    //  * @return BuExpense[] Returns an array of BuExpense objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BuExpense
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
