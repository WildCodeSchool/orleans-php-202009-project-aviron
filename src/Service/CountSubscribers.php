<?php

namespace App\Service;

use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class CountSubscribers
{
    public function countSubscribersWithLabel(
        array $subscriptions,
        ServiceEntityRepository $serviceEntityRepo
    ): array {
        $countedSubscribers = array_column($subscriptions, 'subscribersCount', 'acronym');
        $allLabels = $serviceEntityRepo->findAll();

        $labels = [];
        foreach ($allLabels as $label) {
            $labels[] = $label->getAcronym();
        }
        $labels = array_fill_keys($labels, 0);
        return array_merge($labels, $countedSubscribers);
    }
}
