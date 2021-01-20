<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Licence;
use App\Entity\Subscriber;
use App\Entity\Subscription;
use App\Repository\SeasonRepository;
use DateTime;

class FirstSubscription
{
    private SeasonRepository $seasonRepository;

    public function __construct(SeasonRepository $seasonRepository)
    {
        $this->seasonRepository = $seasonRepository;
    }

    public function filterWith(array $subscribers, LabelInterface $entity, bool $stillRegistered): array
    {
        $subscribersFiltered = [];
        foreach ($subscribers as $subscriber) {
            $firstSubscription = $this->getFirstSubscription($subscriber);
            $isRegistered = $this->isRegisteredLastSeason($subscriber);
            $label =
                $entity instanceof Licence ?
                    $firstSubscription->getLicence()->getAcronym() :
                    (!is_null($firstSubscription->getCategory()) ? $firstSubscription->getCategory()->getLabel() : '');
            if ($label === $entity->getLabel() && (($stillRegistered && $isRegistered) || !$stillRegistered)) {
                $subscribersFiltered[] = $subscriber;
            }
        }
        return $subscribersFiltered;
    }

    private function getFirstSubscription(Subscriber $subscriber): Subscription
    {
        $firstSeason = new DateTime();
        $firstSubscription = new Subscription();
        foreach ($subscriber->getSubscriptions() as $subscription) {
            if ($subscription->getSeason()->getStartingDate() < $firstSeason) {
                $firstSubscription = $subscription;
                $firstSeason = $subscription->getSeason()->getStartingDate();
            }
        }
        return $firstSubscription;
    }

    private function isRegisteredLastSeason(Subscriber $subscriber): bool
    {
        $lastSeason = $this->seasonRepository->findOneBy([], ['name' => 'DESC']);
        $isRegistered = false;
        foreach ($subscriber->getSubscriptions() as $subscription) {
            $isRegistered = $subscription->getSeason()->getStartingDate() === $lastSeason->getStartingDate();
        }
        return $isRegistered;
    }
}
