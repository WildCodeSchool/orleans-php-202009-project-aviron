<?php

namespace App\Repository;

use App\DataFixtures\CategoryFixtures;
use App\Entity\Category;
use App\Entity\Filter;
use App\Entity\Season;
use App\Entity\Subscriber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subscriber|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscriber|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscriber[]    findAll()
 * @method Subscriber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscriber::class);
    }

    /**
     * @SuppressWarnings(PHPMD)
     * @param Filter $filter
     * @return int|mixed|string
     */
    public function findByFilter(Filter $filter)
    {
        $queryBuilder = $this->createQueryBuilder('sr');
        if (!empty($filter->getFromSeason()) && !empty($filter->getToSeason())) {
            $queryBuilder = $this->filterBySeason($filter->getFromSeason(), $filter->getToSeason(), $queryBuilder);
        }
        if (
            !empty($filter->getFromAdherent())
            || $filter->getFromAdherent() === 0
            || !empty($filter->getToAdherent())
        ) {
            $queryBuilder = $this->filterByNumberAdherent(
                $filter->getFromAdherent(),
                $filter->getToAdherent(),
                $queryBuilder
            );
        }
        if (!empty($filter->getGender())) {
            $queryBuilder = $queryBuilder->andWhere('sr.gender = :gender')
                ->setParameter('gender', $filter->getGender());
        }
        if (!empty($filter->getStatus())) {
            $queryBuilder = $this->filterByStatus($filter->getStatus(), $filter->getSeasonStatus(), $queryBuilder);
        }
        if (!empty($filter->getFromCategory()) || !empty($filter->getToCategory())) {
            $queryBuilder = $this->filterByCategory(
                $filter->getFromCategory(),
                $filter->getToCategory(),
                $filter->getSeasonCategory(),
                $queryBuilder
            );
        }
        $queryBuilder = $queryBuilder->orderBy('sr.licenceNumber');

        return $queryBuilder->getQuery()->getResult();
    }

    private function filterBySeason(Season $fromSeason, Season $toSeason, QueryBuilder $queryBuilder): QueryBuilder
    {
        $queryBuilder = $queryBuilder->join('sr.subscriptions', 'sn')
            ->join('sn.season', 's')
            ->where('s.startingDate BETWEEN :fromSeason AND :toSeason')
            ->setParameter('fromSeason', $fromSeason->getStartingDate())
            ->setParameter('toSeason', $toSeason->getStartingDate());
        return $queryBuilder;
    }

    private function filterByNumberAdherent(
        ?int $fromAdherent,
        ?int $toAdherent,
        QueryBuilder $queryBuilder
    ): QueryBuilder {
        if (!empty($fromAdherent) || $fromAdherent === 0) {
            if (!empty($toAdherent)) {
                $queryBuilder = $queryBuilder->andWhere('sr.licenceNumber BETWEEN :fromAdherent AND :toAdherent')
                    ->setParameter('fromAdherent', $fromAdherent)
                    ->setParameter('toAdherent', $toAdherent);
            } else {
                $queryBuilder = $queryBuilder->andWhere('sr.licenceNumber >= :fromAdherent')
                    ->setParameter('fromAdherent', $fromAdherent);
            }
        } elseif (!empty($toAdherent)) {
            $queryBuilder = $queryBuilder->andWhere('sr.licenceNumber <= :toAdherent')
                ->setParameter('toAdherent', $toAdherent);
        }
        return $queryBuilder;
    }

    private function filterByStatus(?array $status, Season $seasonStatus, QueryBuilder $queryBuilder): QueryBuilder
    {
        $queryBuilder = $queryBuilder->andWhere('sn.status IN (:status)')
            ->andWhere('sn.season = :season')
            ->setParameter('status', $status)
            ->setParameter('season', $seasonStatus);
        return $queryBuilder;
    }

    private function filterByCategory(
        ?Category $fromCategory,
        ?Category $toCategory,
        Season $seasonCategory,
        QueryBuilder $queryBuilder
    ): QueryBuilder {
        $categoryRepository = $this->getEntityManager()->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $firstCategory = 0;
        $lastCategory = 0;
        foreach ($categories as $key => $value) {
            $labelFirst = !is_null($fromCategory) ? $fromCategory->getLabel() : ' ';
            $labelLast = !is_null($toCategory) ? $toCategory->getLabel() : ' ';
            if ($value->getLabel() === $labelFirst) {
                $firstCategory = $key;
            }
            if ($value->getLabel() === $labelLast) {
                $lastCategory = $key;
            }
        }

        $categoriesSelected = array_slice($categories, $firstCategory, $lastCategory - $firstCategory + 1);

        $queryBuilder = $queryBuilder->andWhere('sn.category IN (:categories)')
            ->andWhere('sn.season = :season')
            ->setParameter('categories', $categoriesSelected)
            ->setParameter('season', $seasonCategory);
        return $queryBuilder;
    }
}
