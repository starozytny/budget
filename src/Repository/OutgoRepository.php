<?php

namespace App\Repository;

use App\Entity\Outgo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Outgo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outgo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outgo[]    findAll()
 * @method Outgo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutgoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Outgo::class);
    }

    // /**
    //  * @return Outgo[] Returns an array of Outgo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Outgo
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
