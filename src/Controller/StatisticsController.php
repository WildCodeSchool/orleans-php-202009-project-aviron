<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/statistics", name="statistics_")
 */
class StatisticsController extends AbstractController
{
    /**
     * @Route("/general", name="general")
     * @SuppressWarnings(PHPMD.LongVariable)
     * @param SeasonRepository $seasonRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param LicenceRepository $licenceRepository
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function generalStatistics(
        SeasonRepository $seasonRepository,
        SubscriptionRepository $subscriptionRepository,
        LicenceRepository $licenceRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $subscriptions = [];
        $categories = $categoryRepository->findAll();
        $licences = $licenceRepository->findAll();
        $seasons = $seasonRepository->findAll();
        $totalPerSeason = $subscriptionRepository->totalPerSeason();

        foreach ($categories as $category) {
            foreach ($licences as $licence) {
                $subscriptions[$category->getLabel()][$licence->getAcronym()] =
                    $subscriptionRepository->findSubscriptionsBySeason(
                        $category->getLabel(),
                        $licence->getAcronym()
                    );
            }
        }

        return $this->render('statistics/general.html.twig', [
            'statistics' => $subscriptions,
            'seasons' => $seasons,
            'categories' => $categories,
            'licences' => $licences,
            'totalPerSeason' => $totalPerSeason
        ]);
    }
}
