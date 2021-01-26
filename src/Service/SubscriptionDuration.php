<?php

namespace App\Service;

use App\Entity\Subscriber;

class SubscriptionDuration
{
    private Registration $registration;

    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    public function filterBy(array $subscribers, int $duration, bool $orMore, bool $stillRegistered): array
    {
        return array_filter($subscribers, function ($subscriber) use ($duration, $orMore, $stillRegistered) {
            $number = $this->getOnlyConsecutiveYears($subscriber);
            $isRegistered = $this->registration->isRegisteredLastSeason($subscriber);
            if (($orMore && ($number >= $duration)) || (!$orMore && ($number === $duration))) {
                if (($stillRegistered && $isRegistered) || !$stillRegistered) {
                    return $subscriber;
                }
            }
            return false;
        });
    }

    /**
     * Retourne le nombre de saisons consécutives seulement si celui-ci est égal au nombre de saisons total,
     * retourne 0 sinon
     *
     * @param Subscriber $subscriber
     * @return int
     */
    private function getOnlyConsecutiveYears(Subscriber $subscriber): int
    {
        $seasons = [];
        foreach ($subscriber->getSubscriptions() as $subscription) {
            $seasons[] = $subscription->getSeason()->getName();
        }
        sort($seasons);
        $numberYears = 1;
        for ($i = 0; $i < count($seasons) - 1; $i++) {
            if (!is_null($seasons[$i]) && !is_null($seasons[$i + 1])) {
                if (substr($seasons[$i], 5) === substr($seasons[$i + 1], 0, 4)) {
                    $numberYears++;
                }
            }
        }
        return $numberYears === count($seasons) ? $numberYears : 0;
    }
}
