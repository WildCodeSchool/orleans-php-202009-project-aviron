<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriptionRepository;
use Mukadi\Chart\Chart as MChart;
use Mukadi\ChartJSBundle\Chart\Builder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * @SuppressWarnings(PHPMD)
 * @Route("/statistics", name="statistics_")
 */
class StatisticsController extends AbstractController
{
    private const TOTAL_PALETTE_F = '#F74B75';

    private const TOTAL_PALETTE_H = '#135B79';

    private const LICENCES_PALETTE = [
        'Découverte' => '#37cf9b',
        'Compétition' => '#6688c3',
        'Universitaire' => '#a65bd7',
        'Indoor' => '#f2e13c',
    ];

    private const LICENCES_NAME = [
        'D' => 'Découverte',
        'C' => 'Compétition',
        'U' => 'Universitaire',
        'I' => 'Indoor',
    ];

    private const CATEGORIES_NAME = [
        'Benjamin' => ['J9', 'J12'],
        'Minime' => ['J13', 'J14'],
        'Cadet' => ['J15', 'J16'],
        'Junior' => ['J17', 'J18'],
        'Senior B' => ['S-23'],
        'Senior A' => ['S'],
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
        Builder $totalBuilder,
        ChartBuilderInterface $chartBuilder
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
        $totalChart = $totalBuilder->buildChart('total-chart', MChart::BAR);

        $totalLicences = $subscriptionRepository->totalLicencesPerSeason();

        $licencesData = [
            self::LICENCES_NAME['C'] => [],
            self::LICENCES_NAME['D'] => [],
            self::LICENCES_NAME['U'] => [],
            self::LICENCES_NAME['I'] => [],
        ];

        $seasonNames = [];
        foreach ($seasons as $season) {
            $seasonNames[] = $season->getName();
        }

        $licencesData[self::LICENCES_NAME['C']] = array_fill(0, count($seasonNames), 0);
        $licencesData[self::LICENCES_NAME['D']] = array_fill(0, count($seasonNames), 0);
        $licencesData[self::LICENCES_NAME['U']] = array_fill(0, count($seasonNames), 0);
        $licencesData[self::LICENCES_NAME['I']] = array_fill(0, count($seasonNames), 0);


        for ($i = 0; $i < count($seasonNames); $i++) {
            for ($j = 0; $j < count($totalLicences); $j++) {
                if ($totalLicences[$j]['seasonName'] == $seasonNames[$i]) {
                    if ($totalLicences[$j]['name'] == self::LICENCES_NAME['C']) {
                        $licencesData[self::LICENCES_NAME['C']][$i] += $totalLicences[$j]['total'];
                    } elseif ($totalLicences[$j]['name'] == self::LICENCES_NAME['D']) {
                        $licencesData[self::LICENCES_NAME['D']][$i] += $totalLicences[$j]['total'];
                    } elseif ($totalLicences[$j]['name'] == self::LICENCES_NAME['U']) {
                        $licencesData[self::LICENCES_NAME['U']][$i] += $totalLicences[$j]['total'];
                    } elseif ($totalLicences[$j]['name'] == self::LICENCES_NAME['I']) {
                        $licencesData[self::LICENCES_NAME['I']][$i] += $totalLicences[$j]['total'];
                    }
                }
            }
        }

        $licencesChart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $licencesChart->setData([
            'labels' => $seasonNames,
            'datasets' => [
                [
                    'label' => self::LICENCES_NAME['C'],
                    'backgroundColor' => self::LICENCES_PALETTE['Compétition'],
                    'data' => $licencesData[self::LICENCES_NAME['C']],
                ],
                [
                    'label' => self::LICENCES_NAME['D'],
                    'backgroundColor' => self::LICENCES_PALETTE['Découverte'],
                    'data' => $licencesData[self::LICENCES_NAME['D']],
                ],
                [
                    'label' => self::LICENCES_NAME['U'],
                    'backgroundColor' => self::LICENCES_PALETTE['Universitaire'],
                    'data' => $licencesData[self::LICENCES_NAME['U']],
                ],
                [
                    'label' => self::LICENCES_NAME['I'],
                    'backgroundColor' => self::LICENCES_PALETTE['Indoor'],
                    'data' => $licencesData[self::LICENCES_NAME['I']],
                ],
            ]
        ]);
        $licencesChart->setOptions([
            "scales" => [
                "xAxes" => [
                    [
                        "stacked" => true
                    ]
                ],
                "yAxes" => [
                    [
                        "stacked" => true
                    ]
                ],
            ]
        ]);

        $totalCategories = $subscriptionRepository->totalCategoriesPerSeason();

        $licencesData = [
            self::LICENCES_NAME['C'] => [],
            self::LICENCES_NAME['D'] => [],
            self::LICENCES_NAME['U'] => [],
            self::LICENCES_NAME['I'] => [],
        ];

        $seasonNames = [];
        foreach ($seasons as $season) {
            $seasonNames[] = $season->getName();
        }

        $licencesData[self::LICENCES_NAME['C']] = array_fill(0, count($seasonNames), 0);
        $licencesData[self::LICENCES_NAME['D']] = array_fill(0, count($seasonNames), 0);
        $licencesData[self::LICENCES_NAME['U']] = array_fill(0, count($seasonNames), 0);
        $licencesData[self::LICENCES_NAME['I']] = array_fill(0, count($seasonNames), 0);


        for ($i = 0; $i < count($seasonNames); $i++) {
            for ($j = 0; $j < count($totalLicences); $j++) {
                if ($totalLicences[$j]['seasonName'] == $seasonNames[$i]) {
                    if ($totalLicences[$j]['name'] == self::LICENCES_NAME['C']) {
                        $licencesData[self::LICENCES_NAME['C']][$i] += $totalLicences[$j]['total'];
                    } elseif ($totalLicences[$j]['name'] == self::LICENCES_NAME['D']) {
                        $licencesData[self::LICENCES_NAME['D']][$i] += $totalLicences[$j]['total'];
                    } elseif ($totalLicences[$j]['name'] == self::LICENCES_NAME['U']) {
                        $licencesData[self::LICENCES_NAME['U']][$i] += $totalLicences[$j]['total'];
                    } elseif ($totalLicences[$j]['name'] == self::LICENCES_NAME['I']) {
                        $licencesData[self::LICENCES_NAME['I']][$i] += $totalLicences[$j]['total'];
                    }
                }
            }
        }

        $licencesChart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $licencesChart->setData([
            'labels' => $seasonNames,
            'datasets' => [
                [
                    'label' => self::LICENCES_NAME['C'],
                    'backgroundColor' => self::LICENCES_PALETTE['Compétition'],
                    'data' => $licencesData[self::LICENCES_NAME['C']],
                ],
                [
                    'label' => self::LICENCES_NAME['D'],
                    'backgroundColor' => self::LICENCES_PALETTE['Découverte'],
                    'data' => $licencesData[self::LICENCES_NAME['D']],
                ],
                [
                    'label' => self::LICENCES_NAME['U'],
                    'backgroundColor' => self::LICENCES_PALETTE['Universitaire'],
                    'data' => $licencesData[self::LICENCES_NAME['U']],
                ],
                [
                    'label' => self::LICENCES_NAME['I'],
                    'backgroundColor' => self::LICENCES_PALETTE['Indoor'],
                    'data' => $licencesData[self::LICENCES_NAME['I']],
                ],
            ]
        ]);
        $licencesChart->setOptions([
            "scales" => [
                "xAxes" => [
                    [
                        "stacked" => true
                    ]
                ],
                "yAxes" => [
                    [
                        "stacked" => true
                    ]
                ],
            ]
        ]);

        return $this->render('statistics/general.html.twig', [
            'statistics' => $subscriptions,
            'seasons' => $seasons,
            'categories' => $categories,
            'licences' => $licences,
            'totalPerSeason' => $totalPerSeason,
            'grandTotal' => $grandTotalPerSeason,
            'totalChart' => $totalChart,
            'licencesChart' => $licencesChart
        ]);
    }
}
