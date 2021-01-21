<?php

namespace App\Service;

use App\Entity\Subscriber;
use App\Repository\SeasonRepository;

class Registration
{
    private SeasonRepository $seasonRepository;

    public function __construct(SeasonRepository $seasonRepository)
    {
        $this->seasonRepository = $seasonRepository;
    }

    public function isRegisteredLastSeason(Subscriber $subscriber): bool
    {
        $lastSeason = $this->seasonRepository->findOneBy([], ['name' => 'DESC']);
        $seasons = [];
        foreach ($subscriber->getSubscriptions() as $subscription) {
            $seasons[] = $subscription->getSeason()->getName();
        }
        rsort($seasons);
        return $seasons[0] === $lastSeason->getName();
    }
}
