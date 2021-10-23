<?php

namespace App\Repository\Budget;

use App\Entity\Budget\BuOutcome;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BuOutcome|null find($id, $lockMode = null, $lockVersion = null)
 * @method BuOutcome|null findOneBy(array $criteria, array $orderBy = null)
 * @method BuOutcome[]    findAll()
 * @method BuOutcome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuOutcomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuOutcome::class);
    }

    // /**
    //  * @return BuOutcome[] Returns an array of BuOutcome objects
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
    public function findOneBySomeField($value): ?BuOutcome
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
