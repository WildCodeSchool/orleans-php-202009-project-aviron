<?php

namespace App\Service;

use App\Entity\Subscription;
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
            $subscription->getSubscriber()->getLicenceNumber() > $previousSeason[count($previousSeason) - 1]
            && $subscription->getStatus() === ''
        ) {
            $status = self::TRANSFER;
        }

        return $status;
    }

    public function calculate(Subscription $subscription, Collection $subscriptions): ?string
    {
        $status = $subscription->getStatus();
        if ($this->hasPreviousYear($subscription, $subscriptions)) {
            $status = self::RENEWAL;
        } elseif ($this->hasPreviousSeason($subscription, $subscriptions)) {
            $status = self::RESUMED;
        }

        return $status;
    }

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
                (int) ($presentSubscription->getSeason()->getEndingDate()->format('Y')) + 1
                <= $subscription->getSeason()->getEndingDate()->format('Y')
                && $subscription->getStatus() === ''
            ) {
                $hasPreviousSeason = true;
            }
        }
        return $hasPreviousSeason;
    }
}
