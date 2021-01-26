<?php

namespace App\Repository;

use App\Entity\Filter;
use App\Entity\PyramidFilter;
use App\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Season find($id, $lockMode = null, $lockVersion = null)
 * @method Season findOneBy(array $criteria, array $orderBy = null)
 * @method Season[]    findAll()
 * @method Season[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeasonRepository extends ServiceEntityRepository
{
    public const LIMIT_NUMBER_SEASONS = 15;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Season::class);
    }

    /**
     * @param Filter|PyramidFilter $filter
     * @return int|mixed|string
     */
    public function findByFilter($filter)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        if (!empty($filter->getFromSeason()) && !empty($filter->getToSeason())) {
            $queryBuilder = $queryBuilder->where('s.startingDate BETWEEN :fromSeason AND :toSeason')
                ->setParameter('fromSeason', $filter->getFromSeason()->getStartingDate())
                ->setParameter('toSeason', $filter->getToSeason()->getStartingDate())
                ->orderBy('s.name');
        }
        return $queryBuilder->getQuery()->getResult();
    }
}
