<?php

namespace App\Service;

use App\Entity\Season;
use App\Entity\Subscriber;
use App\Entity\Subscription;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class StatusCalculator
{
    private const TRANSFER = 'T';
    private const NEW = 'N';
    private const RENEWAL = 'R';
    private const RESUMED = 'P';

    /**
     * @param Subscription $subscription
     * @param array<int> $previousSeason
     * @return string|null
     */
    public function calculateNew(Subscription $subscription, array $previousSeason): ?string
    {
        $status = $subscription->getStatus();
        //si $previousSeason est vide, cela signifie qu'on est sur la première saison
        if (
            empty($previousSeason)
            || $subscription->getSubscriber()->getLicenceNumber() > $previousSeason[count($previousSeason) - 1]
        ) {
            $status = self::NEW;
        } elseif (
            $subscription->getSubscriber()->getLicenceNumber() < $previousSeason[count($previousSeason) - 1]
            && $subscription->getStatus() === ''
        ) {
            $status = self::TRANSFER;
        }

        return $status;
    }

    /**
     * @param array<Season> $seasons
     * @param array<Subscriber> $subscribers
     */
    public function calculate(array $seasons, array $subscribers): void
    {
        $previousSeason = [];
        $currentSeason = [];
        foreach ($seasons as $season) {
            foreach ($season->getSubscriptions() as $subscriptionSeason) {
                asort($previousSeason);
                $subscriptionSeason->setStatus($this->calculateNew(
                    $subscriptionSeason,
                    $previousSeason
                ));
                $currentSeason[] = $subscriptionSeason->getSubscriber()->getLicenceNumber();
            }
            $previousSeason = [];
            $previousSeason = $currentSeason;
            $currentSeason = [];
        }
        foreach ($subscribers as $subscriber) {
            $subscriptions = $subscriber->getSubscriptions();
            foreach ($subscriptions as $subscription) {
                if ($subscription->getSeason() !== $seasons[0]) {
                    if ($this->hasPreviousYear($subscription, $subscriptions)) {
                        $subscription->setStatus(self::RENEWAL);
                    } elseif ($this->hasPreviousSeason($subscription, $subscriptions)) {
                        $subscription->setStatus(self::RESUMED);
                    }
                }
            }
        }
    }

    /*
    * Vérifie si le rameur a été inscrit dans ce club à la saison n-1
    */
    private function hasPreviousYear(Subscription $presentSubscription, Collection $subscriptions): bool
    {
        $hasNextYear = false;
        foreach ($subscriptions as $subscription) {
            if (
                $presentSubscription->getSeason()->getStartingDate()->format('Y')
                === $subscription->getSeason()->getEndingDate()->format('Y')
            ) {
                $hasNextYear = true;
            }
        }
        return $hasNextYear;
    }

    /*
     * Vérifie si le rameur a été inscrit dans ce club lors d'une précédente saison, mais pas la saison n-1
     */
    private function hasPreviousSeason(Subscription $presentSubscription, Collection $subscriptions): bool
    {
        $hasPreviousSeason = false;
        foreach ($subscriptions as $subscription) {
            if (
                (int) ($presentSubscription->getSeason()->getStartingDate()->format('Y'))
                >= $subscription->getSeason()->getEndingDate()->format('Y') + 1
            ) {
                $hasPreviousSeason = true;
            }
        }
        return $hasPreviousSeason;
    }
}
