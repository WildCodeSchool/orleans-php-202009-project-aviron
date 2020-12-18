<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\AST\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    /**
     * @param string|null $licenceAcronym
     * @param string|null $seasonName
     * @return Subscription
     * @throws NonUniqueResultException
     */
    public function findAllSubscribersForActualSeason(?string $licenceAcronym, ?string $seasonName)
    {
        return $this->createQueryBuilder('sub')
            ->select('COUNT(sub.subscriber)')
            ->innerJoin('App\Entity\Licence', 'l', 'WITH', 'l.id = sub.licence')
            ->innerJoin('App\Entity\Season', 's', 'WITH', 's.id = sub.season')
            ->where('l.acronym = :licenceAcronym')
            ->setParameter('licenceAcronym', $licenceAcronym)
            ->andWhere('s.name = :seasonName')
            ->setParameter('seasonName', $seasonName)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string|null $licenceAcronym
     * @param string|null $seasonName
     * @param string|null $categoryName
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function findAllYoungSubscribersForActualSeason(
        ?string $licenceAcronym,
        ?string $seasonName,
        ?string $categoryName
    ) {
        return $this->createQueryBuilder('sub')
            ->select('COUNT(sub.subscriber)')
            ->innerJoin('App\Entity\Licence', 'l', 'WITH', 'l.id = sub.licence')
            ->innerJoin('App\Entity\Season', 's', 'WITH', 's.id = sub.season')
            ->innerJoin('App\Entity\Category', 'c', 'WITH', 'c.id = sub.category')
            ->where('l.acronym = :licenceAcronym')
            ->setParameter('licenceAcronym', $licenceAcronym)
            ->andWhere('s.name = :seasonName')
            ->setParameter('seasonName', $seasonName)
            ->andWhere('c.label LIKE :categoryName')
            ->setParameter('categoryName', $categoryName . '%')
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return Subscription[] Returns an array of Subscription objects
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
    public function findOneBySomeField($value): ?Subscription
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
