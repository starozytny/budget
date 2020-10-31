<?php

namespace App\Repository;

use App\Entity\RegularSpend;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RegularSpend|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegularSpend|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegularSpend[]    findAll()
 * @method RegularSpend[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegularSpendRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegularSpend::class);
    }

    // /**
    //  * @return RegularSpend[] Returns an array of RegularSpend objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RegularSpend
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
