<?php

namespace App\Service;

use App\Entity\Licence;
use App\Repository\SubscriptionRepository;

/**
* @SuppressWarnings(PHPMD.LongVariable)
*/
class PyramidCalculator
{
    private SubscriptionRepository $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }


    public function getRenewalPyramidCounts(array $seasons, ?Licence $licence): array
    {
        $renewalPyramid = [];

        for ($i = 0; $i < count($seasons); $i++) {
            $seasonSubscriptions = $this->subscriptionRepository->findBy([
                'season' => $seasons[$i],
                'licence' => $licence
            ]);

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
                            $seasonSubscriberSeasons[] = $seasonSubscriberSubscription->getSeason()->getName();
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
                        $renewsSeason[$index] / $seasonReferenceCount * 100,
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
}
