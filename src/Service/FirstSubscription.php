<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Licence;
use App\Entity\Subscriber;
use App\Entity\Subscription;
use DateTime;

class FirstSubscription
{
    private Registration $registration;

    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    public function filterWith(array $subscribers, LabelInterface $entity, bool $stillRegistered): array
    {
        $subscribersFiltered = [];
        foreach ($subscribers as $subscriber) {
            $firstSubscription = $this->getFirstSubscription($subscriber);
            $isRegistered = $this->registration->isRegisteredLastSeason($subscriber);
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
}
