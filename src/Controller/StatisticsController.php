<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriptionRepository;
use Mukadi\Chart\Chart;
use Mukadi\ChartJSBundle\Chart\Builder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/statistics", name="statistics_")
 */
class StatisticsController extends AbstractController
{
    private const TOTAL_PALETTE = [
        '#F74B75',
        '#135B79',
        '#F74B75',
        '#135B79',
        '#F74B75',
        '#135B79',
        '#F74B75',
        '#135B79',
        '#F74B75',
        '#135B79',
        '#F74B75',
        '#135B79',
        '#F74B75',
        '#135B79',
        ];
    /**
     * @Route("/general", name="general")
     * @SuppressWarnings(PHPMD.LongVariable)
     * @param SeasonRepository $seasonRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param LicenceRepository $licenceRepository
     * @param CategoryRepository $categoryRepository
     * @param Builder $totalBuilder
     * @return Response
     */
    public function generalStatistics(
        SeasonRepository $seasonRepository,
        SubscriptionRepository $subscriptionRepository,
        LicenceRepository $licenceRepository,
        CategoryRepository $categoryRepository,
        Builder $totalBuilder
    ): Response {
        $subscriptions = [];
        $categories = $categoryRepository->findAll();
        $licences = $licenceRepository->findAll();
        $seasons = $seasonRepository->findAll();
        $totalPerSeason = $subscriptionRepository->totalPerSeason();
        $grandTotalPerSeason = $subscriptionRepository->grandTotalPerSeason();

        foreach ($categories as $category) {
            foreach ($licences as $licence) {
                $subscriptions[$category->getLabel()][$licence->getAcronym()] =
                    $subscriptionRepository->findSubscriptionsBySeason(
                        $category->getLabel(),
                        $licence->getAcronym()
                    );
            }
        }
        dump($totalPerSeason);
        $queryGrandTotalPerSeason = $subscriptionRepository->getQueryForTotalPerSeason();

        $totalBuilder
            ->query($queryGrandTotalPerSeason)
            ->addDataSet('total', 'Subscribers', [
                "backgroundColor" => self::TOTAL_PALETTE
            ])
            ->labels('seasonName');
        $totalChart = $totalBuilder->buildChart('total-chart', Chart::BAR);
        $totalChart->pushOptions([
            'scales' => ([
                'xAxes' => ([
                    'stacked' => 'true'
                ])
            ])
        ]);


//        $queryGrandTotalPerSeason = $subscriptionRepository->getQueryForGrandTotalPerSeason();
//
//        $totalBuilder
//            ->query($queryGrandTotalPerSeason)
//            ->addDataSet('total', 'Subscribers', [
//                "backgroundColor" => self::TOTAL_PALETTE
//            ])
//            ->labels('seasonName');
//        $totalChart = $totalBuilder->buildChart('total-chart', Chart::BAR);
//        $totalChart->pushOptions([
//            'legend' => ([
//                'position' => 'bottom',
//            ]),
//        ]);

        return $this->render('statistics/general.html.twig', [
            'statistics' => $subscriptions,
            'seasons' => $seasons,
            'categories' => $categories,
            'licences' => $licences,
            'totalPerSeason' => $totalPerSeason,
            'grandTotal' => $grandTotalPerSeason,
            'totalChart' => $totalChart
        ]);
    }
}
