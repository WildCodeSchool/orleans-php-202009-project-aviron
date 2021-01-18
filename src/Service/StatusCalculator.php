<?php

namespace App\Service;

use App\Entity\Season;
use App\Entity\Status;
use App\Entity\Subscriber;
use App\Entity\Subscription;
use App\Repository\StatusRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */

class StatusCalculator
{
    /**
     * @var StatusRepository
     */
    private StatusRepository $statusRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var SubscriptionRepository
     */
    private SubscriptionRepository $subscriptionRepository;

    /**
     * StatusCalculator constructor.
     * @param EntityManagerInterface $entityManager
     * @param StatusRepository $statusRepository
     * @param SubscriptionRepository $subscriptionRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        StatusRepository $statusRepository,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->entityManager = $entityManager;
        $this->statusRepository = $statusRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * @param Subscription $subscription
     * @param Season|null $previousSeason
     * @param int $previousSeasonLastLicence
     * @return Status|null
     */
    public function calculateNew(
        Subscription $subscription,
        ?Season $previousSeason,
        int $previousSeasonLastLicence
    ): ?Status {
          $status = $subscription->getStatus();
        //si $previousSeason est vide, cela signifie qu'on est sur la première saison
        if (
            !$previousSeason instanceof Season
            || $subscription->getSubscriber()->getLicenceNumber() > $previousSeasonLastLicence
        ) {
            $status = $this->statusRepository->findOneBy(['label' => 'N']);
        } elseif (
            $subscription->getSubscriber()->getLicenceNumber() < $previousSeasonLastLicence
            && $subscription->getStatus() == null
        ) {
            $subscription->setStatus($this->statusRepository->findOneBy(['label' => 'T']));
        }

        return $status;
    }

    /**
     * @param array<Season> $seasons
     * @param array<Subscriber> $subscribers
     */
    public function calculate(array $seasons, array $subscribers): void
    {
        asort($seasons);

        for ($i = 0; $i < count($seasons); $i++) {
            if ($i === 0) {
                $previousSeason = null;
                $previousSeasonLastLicence = 0;
            } else {
                $previousSeason = $seasons[$i - 1];
                $previousSeasonLastLicence = $this
                    ->subscriptionRepository
                    ->getLastSubscriber($previousSeason->getName())[0]['licenceNumber'];
            }
            foreach ($seasons[$i]->getSubscriptions() as $subscriptionSeason) {
                $subscriptionSeason->setStatus($this->calculateNew(
                    $subscriptionSeason,
                    $previousSeason,
                    $previousSeasonLastLicence
                ));
            }

            foreach ($subscribers as $subscriber) {
                $subscriptions = $subscriber->getSubscriptions();
                foreach ($subscriptions as $subscription) {
                    if ($subscription->getSeason() !== $seasons[0]) {
                        if ($this->hasPreviousYear($subscription, $subscriptions)) {
                            $subscription->setStatus($this->statusRepository->findOneBy(['label' => 'R']));
                        } elseif ($this->hasPreviousSeason($subscription, $subscriptions)) {
                            $subscription->setStatus($this->statusRepository->findOneBy(['label' => 'P']));
                        } else {
                            $subscription->setStatus($this->statusRepository->findOneBy(['label' => 'N']));
                        }
                    }
                }
            }
        }
        $this->entityManager->flush();
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
