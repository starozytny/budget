<?php

namespace App\Repository;

use App\Entity\Economy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Economy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Economy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Economy[]    findAll()
 * @method Economy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EconomyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Economy::class);
    }

    // /**
    //  * @return Economy[] Returns an array of Economy objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Economy
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
