<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Season;
use App\Entity\Subscriber;
use App\Repository\CategoryRepository;
use App\Repository\SeasonRepository;

class Registration
{
    private SeasonRepository $seasonRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(SeasonRepository $seasonRepository, CategoryRepository $categoryRepository)
    {
        $this->seasonRepository = $seasonRepository;
        $this->categoryRepository = $categoryRepository;
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

    public function filterWithSeasonAndWithStatus(array $subscribers, array $status, Season $season): array
    {
        return array_filter($subscribers, function ($subscriber) use ($status, $season) {
            foreach ($subscriber->getSubscriptions() as $subscription) {
                if (
                    in_array($subscription->getStatus(), $status)
                    && $subscription->getSeason() === $season
                ) {
                    return $subscriber;
                }
            }
            return false;
        });
    }

    public function filterWithSeasonAndWithLicence(array $subscribers, array $licences, Season $season): array
    {
        return array_filter($subscribers, function ($subscriber) use ($licences, $season) {
            foreach ($subscriber->getSubscriptions() as $subscription) {
                if (
                    in_array($subscription->getLicence(), $licences)
                    && $subscription->getSeason() === $season
                ) {
                    return $subscriber;
                }
            }
            return false;
        });
    }

    public function filterWithSeasonAndWithCategories(
        array $subscribers,
        ?Category $fromCategory,
        ?Category $toCategory,
        Season $season
    ): array {
        $categories = $this->categoryRepository->findAll();
        $firstCategory = 0;
        $lastCategory = 0;
        foreach ($categories as $key => $value) {
            $labelFirst = !is_null($fromCategory) ? $fromCategory->getLabel() : ' ';
            $labelLast = !is_null($toCategory) ? $toCategory->getLabel() : ' ';
            if ($value->getLabel() === $labelFirst) {
                $firstCategory = $key;
            }
            if ($value->getLabel() === $labelLast) {
                $lastCategory = $key;
            }
        }

        $categoriesSelected = array_slice($categories, $firstCategory, $lastCategory - $firstCategory + 1);
        return array_filter($subscribers, function ($subscriber) use ($categoriesSelected, $season) {
            foreach ($subscriber->getSubscriptions() as $subscription) {
                if (
                    in_array($subscription->getCategory(), $categoriesSelected)
                    && $subscription->getSeason() === $season
                ) {
                    return $subscriber;
                }
            }
            return false;
        });
    }
}
