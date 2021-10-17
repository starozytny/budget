<?php

namespace App\Repository\Budget;

use App\Entity\Budget\BuPlanning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BuPlanning|null find($id, $lockMode = null, $lockVersion = null)
 * @method BuPlanning|null findOneBy(array $criteria, array $orderBy = null)
 * @method BuPlanning[]    findAll()
 * @method BuPlanning[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuPlanningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuPlanning::class);
    }

    // /**
    //  * @return BuPlanning[] Returns an array of BuPlanning objects
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
    public function findOneBySomeField($value): ?BuPlanning
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
