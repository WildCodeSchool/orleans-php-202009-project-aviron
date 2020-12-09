<?php

namespace App\Repository;

use App\Entity\SubscriberSeason;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubscriberSeason|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubscriberSeason|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubscriberSeason[]    findAll()
 * @method SubscriberSeason[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriberSeasonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscriberSeason::class);
    }

    // /**
    //  * @return SubscriberSeason[] Returns an array of SubscriberSeason objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SubscriberSeason
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
