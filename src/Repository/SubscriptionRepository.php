<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Licence;
use App\Entity\PyramidFilter;
use App\Entity\Season;
use App\Entity\Status;
use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\AST\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @SuppressWarnings(PHPMD)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }
    public function grandTotalPerSeason(): array
    {
        return $this->createQueryBuilder('subscription')
            ->select('COUNT(subscription.subscriber) AS total, season.name')
            ->leftJoin('App\Entity\Season', 'season', 'WITH', 'subscription.season = season.id')
            ->groupBy('season.name')
            ->orderBy('season.name')
            ->getQuery()
            ->getResult();
    }

    public function totalPerSeason(): array
    {
        return $this->createQueryBuilder('subscription')
            ->select('COUNT(subscription.subscriber) AS total, season.name, subscriber.gender')
            ->leftJoin('App\Entity\Season', 'season', 'WITH', 'subscription.season = season.id')
            ->leftJoin('App\Entity\Subscriber', 'subscriber', 'WITH', 'subscription.subscriber = subscriber.id')
            ->groupBy('season.name, subscriber.gender')
            ->orderBy('season.name')
            ->getQuery()
            ->getResult();
    }

    public function getQueryForTotalPerSeason(): string
    {
        return "SELECT case when subscriber.gender= 'H' then count(subscription.subscriber) else 0 end AS totalMale,
        case when subscriber.gender= 'F' then count(subscription.subscriber) else 0 end AS totalFemale,
        season.name AS seasonName
        FROM App\Entity\Subscription subscription
        JOIN App\Entity\Season season
        WITH season.id=subscription.season
        JOIN App\Entity\Subscriber subscriber
        WITH subscriber.id=subscription.subscriber
        GROUP BY seasonName, subscriber.gender";
    }

    public function totalLicencesPerSeason(?string $categoryFilter = ''): array
    {
        $queryBuilder = $this->createQueryBuilder('subscription')
            ->select('COUNT(subscription.subscriber) AS total, licence.name, season.name AS seasonName')
            ->leftJoin('App\Entity\Season', 'season', 'WITH', 'subscription.season = season.id')
            ->leftJoin('App\Entity\Licence', 'licence', 'WITH', 'licence.id=subscription.licence');
        if ($categoryFilter) {
            $queryBuilder->leftJoin('App\Entity\Category', 'category', 'WITH', 'subscription.category = category.id')
                ->where('category.newGroup = :categoryFilter')
                ->setParameter('categoryFilter', $categoryFilter);
        }
        return $queryBuilder->groupBy('season.name, licence.name')
            ->orderBy('season.name')
            ->getQuery()
            ->getResult();
    }
    public function totalCategoriesPerSeason(?string $licenceFilter = ''): array
    {
        $queryBuilder = $this->createQueryBuilder('subscription')
            ->select('COUNT(subscription.subscriber) AS total, category.label, category.newGroup, 
            season.name AS seasonName')
            ->leftJoin('App\Entity\Season', 'season', 'WITH', 'subscription.season = season.id')
            ->leftJoin('App\Entity\Category', 'category', 'WITH', 'category.id=subscription.category');
        if ($licenceFilter) {
            $queryBuilder->leftJoin('App\Entity\Licence', 'licence', 'WITH', 'subscription.licence = licence.id')
                ->where('licence.name = :licenceFilter')
                ->setParameter('licenceFilter', $licenceFilter);
        }
        return $queryBuilder->groupBy('season.name, category.label, category.newGroup')
            ->orderBy('season.name')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string|null $categoryLabel
     * @param string|null $licenceAcronym
     * @return Subscription
     */
    public function findSubscriptionsBySeason(
        ?string $categoryLabel,
        ?string $licenceAcronym
    ) {
        return $this->createQueryBuilder('subscription')
            ->select('COUNT(subscription.subscriber) AS total, season.name, subscriber.gender')
            ->leftJoin('App\Entity\Season', 'season', 'WITH', 'subscription.season = season.id')
            ->leftJoin('App\Entity\Category', 'category', 'WITH', 'subscription.category = category.id')
            ->leftJoin('App\Entity\Licence', 'licence', 'WITH', 'subscription.licence = licence.id')
            ->leftJoin('App\Entity\Subscriber', 'subscriber', 'WITH', 'subscription.subscriber = subscriber.id')
            ->where('licence.acronym = :licenceAcronym')
            ->setParameter('licenceAcronym', $licenceAcronym)
            ->andWhere('category.label = :categoryLabel')
            ->setParameter('categoryLabel', $categoryLabel)
            ->groupBy('season.name, subscriber.gender')
            ->orderBy('season.name')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array|null $status
     * @param string|null $seasonName
     * @param string|null $licenceAcronym
     * @return Subscription
     * @throws NonUniqueResultException
     */
    public function findAllSubscribersForSeasonByLicenceByStatus(
        ?array $status,
        ?string $seasonName,
        ?string $licenceAcronym
    ) {
        return $this->createQueryBuilder('sub')
            ->select('COUNT(sub.subscriber)')
            ->innerJoin('App\Entity\Season', 's', 'WITH', 's.id = sub.season')
            ->innerJoin('App\Entity\Status', 'st', 'WITH', 'st.id = sub.status')
            ->join('App\Entity\Licence', 'l', 'WITH', 'l.id=sub.licence')
            ->andWhere('s.name = :seasonName')
            ->andWhere('st.label IN (:status)')
            ->andWhere('l.acronym = :licenceAcronym')
            ->setParameter('seasonName', $seasonName)
            ->setParameter('status', $status)
            ->setParameter('licenceAcronym', $licenceAcronym)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function subscribersByYearByLicences(?string $season): array
    {
        return $this->createQueryBuilder('subscription')
            ->select('licence.acronym as label, COUNT(subscription.subscriber) as subscribersCount')
            ->leftJoin('App\Entity\Licence', 'licence', 'WITH', 'licence.id=subscription.licence')
            ->innerJoin('App\Entity\Season', 'season', 'WITH', 'season.id=subscription.season')
            ->where('season.name = :season')
            ->setParameter('season', $season)
            ->groupBy('licence.acronym')
            ->getQuery()
            ->getResult();
    }

    public function subscribersByYearByCategories(?string $season): array
    {
        return $this->createQueryBuilder('sub')
            ->select('c.label as label, COUNT(sub.category) as subscribersCount')
            ->leftJoin('App\Entity\Category', 'c', 'WITH', 'c.id = sub.category')
            ->innerJoin('App\Entity\Season', 's', 'WITH', 's.id = sub.season')
            ->where('s.name = :season')
            ->setParameter('season', $season)
            ->groupBy('sub.category')
            ->orderBy('c.id')
            ->getQuery()
            ->getResult();
    }

    public function subscribersByYearByStatus(?string $season): array
    {
        return $this->createQueryBuilder('sub')
            ->select('st.label as label, COUNT(sub.status) as subscribersCount')
            ->leftJoin('App\Entity\Status', 'st', 'WITH', 'st.id = sub.status')
            ->innerJoin('App\Entity\Season', 's', 'WITH', 's.id = sub.season')
            ->where('s.name = :season')
            ->setParameter('season', $season)
            ->groupBy('sub.status')
            ->getQuery()
            ->getResult();
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

    /**
     * @param string|null $seasonName
     * @return array
     */
    public function getLastSubscriber(?string $seasonName)
    {
        return $this->createQueryBuilder('sub')
            ->select('sr.licenceNumber')
            ->innerJoin('App\Entity\Season', 'sn', 'WITH', 'sn.id = sub.season')
            ->innerJoin('App\Entity\Subscriber', 'sr', 'WITH', 'sr.id = sub.subscriber')
            ->where('sn.name = :seasonName')
            ->setParameter('seasonName', $seasonName)
            ->orderBy('sr.licenceNumber', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function getMonthlySubscriptionsByYear(?string $season): array
    {
        return $this->createQueryBuilder('sub')
            ->select('month(sub.subscriptionDate) as month, count(sub.subscriber) as count')
            ->innerJoin('App\Entity\Season', 'sn', 'WITH', 'sn.id = sub.season')
            ->where('sn.name = :seasonName')
            ->setParameter('seasonName', $season)
            ->groupBy('month')
            ->getQuery()
            ->getResult();
    }

    public function findByPyramidFilter(Season $season, ?Licence $licence, ?PyramidFilter $filter): array
    {
        $queryBuilder = $this->createQueryBuilder('sub')
            ->where('sub.season = :season')
            ->setParameter('season', $season)
            ->andWhere('sub.licence = :licence')
            ->setParameter('licence', $licence);

        if (!is_null($filter) && !empty($filter->getGender())) {
            $queryBuilder = $queryBuilder
                ->join('App\Entity\Subscriber', 'sr', 'WITH', 'sr.id = sub.subscriber')
                ->andWhere('sr.gender IN (:gender)')
                ->setParameter('gender', $filter->getGender());
        }

        if (!is_null($filter) && (!empty($filter->getFromCategory()) || !empty($filter->getToCategory()))) {
            $queryBuilder = $this->filterByCategory(
                $filter->getFromCategory(),
                $filter->getToCategory(),
                $queryBuilder
            );
        }

        if (!is_null($filter) && !empty($filter->isNewSubscriber())) {
            $queryBuilder = $this->filterNewSubscribers($queryBuilder);
        }

            return $queryBuilder->getQuery()->getResult();
    }

    private function filterByCategory(
        ?Category $fromCategory,
        ?Category $toCategory,
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

        $queryBuilder = $queryBuilder
            ->andWhere('sub.category IN (:categories)')
            ->setParameter('categories', $categoriesSelected);

        return $queryBuilder;
    }

    private function filterNewSubscribers(QueryBuilder $queryBuilder): QueryBuilder
    {
        $statusRepository = $this->getEntityManager()->getRepository(Status::class);
        $statusNew = [$statusRepository->findOneBy(['label' => 'N']), $statusRepository->findOneBy(['label' => 'T'])];

        $queryBuilder = $queryBuilder
            ->andWhere('sub.status IN (:statusNew)')
            ->setParameter('statusNew', $statusNew);

        return $queryBuilder;
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
