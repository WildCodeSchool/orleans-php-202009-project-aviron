<?php

namespace App\Service;

use App\Entity\Licence;
use App\Entity\PyramidFilter;
use App\Entity\Subscription;
use App\Repository\LicenceRepository;
use App\Repository\SubscriptionRepository;

/**
* @SuppressWarnings(PHPMD.LongVariable)
*/
class PyramidCalculator
{
    private const COMPETITION_LICENCE = 'A';
    private const UNIVERSITY_LICENCE = 'U';

    private SubscriptionRepository $subscriptionRepository;

    private LicenceRepository $licenceRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository, LicenceRepository $licenceRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->licenceRepository = $licenceRepository;
    }


    public function getRenewalPyramidCounts(array $seasons, ?PyramidFilter $filters): array
    {
        $renewalPyramid = [];
        $licenceA = $this->licenceRepository->findOneBy(['acronym' => self::COMPETITION_LICENCE]);
        $licenceU = $this->licenceRepository->findOneBy(['acronym' => self::UNIVERSITY_LICENCE]);

        for ($i = 0; $i < count($seasons); $i++) {
            $seasonSubscriptions = $this->subscriptionRepository
                ->findByPyramidFilter($seasons[$i], $licenceA, $filters);

            $renewalSeason = [];

            for ($j = 0; $j < count($seasons); $j++) {
                if ($j < $i) {
                    $renewalSeason[] = null;
                } elseif ($j === $i) {
                    $renewalSeason[] = count($seasonSubscriptions);
                } else {
                    $renewSubscriptions = 0;
                    foreach ($seasonSubscriptions as $seasonSubscription) {
                        $seasonSubscriberSubscriptions = $seasonSubscription->getSubscriber()->getSubscriptions();
                        $seasonSubscriberSeasons = [];
                        foreach ($seasonSubscriberSubscriptions as $seasonSubscriberSubscription) {
                            $seasonSubscriberSeasons[] = $this
                                ->findSeasonSubscriptionsByLicence(
                                    $seasonSubscriberSubscription,
                                    $licenceA,
                                    $licenceU,
                                    $filters
                                );
                        }
                        if (in_array($seasons[$j]->getName(), $seasonSubscriberSeasons)) {
                            $renewSubscriptions++;
                        }
                    }
                    $renewalSeason[] = $renewSubscriptions;
                }
            }

            $renewalPyramid[$seasons[$i]->getName()] = $renewalSeason;
        }

        return $renewalPyramid;
    }

    private function findSeasonSubscriptionsByLicence(
        Subscription $seasonSubscriberSubscription,
        ?Licence $licenceA,
        ?Licence $licenceU,
        ?PyramidFilter $filters
    ): ?string {
        $seasonSubscriberSeason = '';
        if ($seasonSubscriberSubscription->getLicence() === $licenceA) {
            $seasonSubscriberSeason = $seasonSubscriberSubscription->getSeason()->getName();
        }
        if (
            !is_null($filters) &&
            $filters->isLicenceU() &&
            $seasonSubscriberSubscription->getLicence() === $licenceU
        ) {
            $seasonSubscriberSeason = $seasonSubscriberSubscription->getSeason()->getName();
        }
        return $seasonSubscriberSeason;
    }

    public function getRenewalPyramidPercent(array $renewalPyramid): array
    {
        $renewalPyramidPercent = [];
        foreach ($renewalPyramid as $season => $renewsSeason) {
            $referenceIndex = 0;
            $seasonReferenceCount = 1;
            $yearIndex = 0;
            for ($index = 0; $index < count($renewsSeason); $index++) {
                if ($renewsSeason[$index] === null) {
                    $referenceIndex++;
                } elseif ($renewsSeason[$index] !== null && $index === $referenceIndex) {
                    $seasonReferenceCount = $renewsSeason[$index];
                } else {
                    $renewalPyramidPercent[$season][$yearIndex] = number_format(
                        $seasonReferenceCount > 0 ? $renewsSeason[$index] / $seasonReferenceCount * 100 : 0,
                        1,
                        ',',
                        ' '
                    );
                    $yearIndex++;
                }
            }
        }

        return $renewalPyramidPercent;
    }

    public function getAverageRenewalPercent(array $renewalPyramidPercent): array
    {
        $renewalPercentPerDuration = [];

        foreach ($renewalPyramidPercent as $year) {
            for ($index = 0; $index < count($year); $index++) {
                $renewalPercentPerDuration[$index][] = $year[$index];
            }
        }

        $renewalPerDurationAverage = [];
        foreach ($renewalPercentPerDuration as $renewalPercentYear) {
            $renewalPerDurationAverage[] = number_format(
                array_sum($renewalPercentYear) / count($renewalPercentYear),
                1,
                ',',
                ' '
            );
        }

        return $renewalPerDurationAverage;
    }
}
