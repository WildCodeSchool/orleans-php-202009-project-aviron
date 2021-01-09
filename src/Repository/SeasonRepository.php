<?php

namespace App\Repository;

use App\Entity\Filter;
use App\Entity\Season;
use DateTime;
use DateInterval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Season find($id, $lockMode = null, $lockVersion = null)
 * @method Season findOneBy(array $criteria, array $orderBy = null)
 * @method Season[]    findAll()
 * @method Season[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeasonRepository extends ServiceEntityRepository
{
    private const NUMBER_SEASONS = 15;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Season::class);
    }

    /**
     * @param Filter $filter
     * @return int|mixed|string
     */
    public function findByFilter(Filter $filter)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        if (!empty($filter->getFromSeason()) && !empty($filter->getToSeason())) {
            $queryBuilder = $queryBuilder->where('s.startingDate BETWEEN :fromSeason AND :toSeason')
                ->setParameter('fromSeason', $filter->getFromSeason()->getStartingDate())
                ->setParameter('toSeason', $filter->getToSeason()->getStartingDate());
        }
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return int|mixed|string
     * @throws Exception
     */
    public function findUmpteenth()
    {
        $now = new DateTime('now');
        $interval = new DateInterval('P' . self::NUMBER_SEASONS . 'Y');
        $startingDate = $now->sub($interval)->format('Y');
        $queryBuilder = $this->createQueryBuilder('s')
            ->where('s.name LIKE :start')
            ->setParameter('start', $startingDate . '%');
        return $queryBuilder->getQuery()->getResult();
    }
}
