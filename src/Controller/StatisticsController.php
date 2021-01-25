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
    private const TOTAL_PALETTE_F = '#F74B75';

    private const TOTAL_PALETTE_H = '#135B79';

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
        $queryTotalPerSeason = $subscriptionRepository->getQueryForTotalPerSeason();

        $totalBuilder
            ->query($queryTotalPerSeason)
            ->addDataSet('totalFemale', 'Femmes', [
                "backgroundColor" => self::TOTAL_PALETTE_F,
                "stack" => 'Femmes'
            ])
            ->addDataSet('totalMale', 'Hommes', [
                "backgroundColor" => self::TOTAL_PALETTE_H,
                "stack" => 'Hommes'
            ])
            ->labels('seasonName');
        $totalChart = $totalBuilder->buildChart('total-chart', Chart::BAR);
        $totalChart->pushOptions([
            'scales' => ([
                'xAxes' => ([
                    'stacked' => 'true'
                ]),
                'yAxes' => ([
                    'stacked' => 'true'
                ])
            ])
        ]);


//        $queryLicencesPerSeason = $subscriptionRepository->getQueryForLicencesPerSeason();

//        $totalLicencesBuilder
//            ->query($queryLicencesPerSeason)
//            ->addDataSet('totalD', 'Découverte', [
//                "backgroundColor" => self::LICENCES_PALETTE['Découverte'],
//                "stack" => "stack 0"
//            ])
//            ->addDataSet('totalC', 'Compétition', [
//                "backgroundColor" => self::LICENCES_PALETTE['Compétition'],
//                "stack" => "stack 0"
//            ])
//            ->addDataSet('totalU', 'Universitaire', [
//                "backgroundColor" => self::LICENCES_PALETTE['Universitaire'],
//                "stack" => "stack 0"
//            ])
//            ->addDataSet('totalI', 'Indoor', [
//                "backgroundColor" => self::LICENCES_PALETTE['Indoor'],
//                "stack" => "stack 0"
//            ])
//            ->labels('seasonName');
//        $licencesChart = $totalLicencesBuilder->buildChart('licences-chart', Chart::BAR);
//        $licencesChart->pushOptions([
//            "scales" => [
//                "xAxes" => [
//                    [
//                        "stacked" => true
//                    ]
//                ],
//            ]
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
