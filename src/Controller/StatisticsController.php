<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriptionRepository;
use Mukadi\Chart\Chart as MChart;
use Mukadi\ChartJSBundle\Chart\Builder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * @SuppressWarnings(PHPMD)
 * @Route("/statistiques", name="statistics_")
 */
class StatisticsController extends AbstractController
{
    private const LICENCES_PALETTE = [
        'Découverte' => '#37cf9b',
        'Compétition' => '#6688c3',
        'Universitaire' => '#a65bd7',
        'Indoor' => '#f2e13c',
        'Total' => '#e7e7e7ff'
    ];

    private const LICENCES_NAME = [
        'D' => 'Découverte',
        'C' => 'Compétition',
        'U' => 'Universitaire',
        'I' => 'Indoor',
        'Total' => 'Total'
    ];

    private const CATEGORIES_NAME = [
        "Jeune" => ['J9', 'J10', 'J11', 'J12', 'J13', 'J14'],
        "Junior" => ['J15', 'J16', 'J17', 'J18'],
        "Senior" => ['S','S-23'],
    ];

    private const CATEGORIES_PALETTES = [
        'Jeune' => '#37cf9b',
        'Junior' => '#6688c3',
        'Senior' => '#a65bd7',
        'Total' => '#e7e7e7ff'
    ];

    private const GENDER = [
        'Femme' => 'F',
        'Homme' => 'H',
        'Total' => 'Total'
    ];

    private const GENDER_PALETTE = [
        'F' => '#F87380',
        'H' => '#6688c3',
        'Total' => '#e7e7e7ff'
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
        Request $request,
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
        $seasons = $seasonRepository->findBy([], ['name' => 'ASC']);
        $totalPerSeason = $subscriptionRepository->totalPerSeason();
        $grandTotalPerSeason = $subscriptionRepository->grandTotalPerSeason();
        $categoryFilter = $request->query->get('categoryFilter');
        $licenceFilter = $request->query->get('licenceFilter');


        //construction du tableau du total par licence par catégorie par saison
        foreach ($categories as $category) {
            foreach ($licences as $licence) {
                $subscriptions[$category->getLabel()][$licence->getAcronym()] =
                    $subscriptionRepository->findSubscriptionsBySeason(
                        $category->getLabel(),
                        $licence->getAcronym()
                    );
            }
        }

        //récupération du nom des saisons pour label des graphique
        $seasonNames = [];
        foreach ($seasons as $season) {
            $seasonNames[] = $season->getName();
        }

        //construction graphique par genre
        foreach (self::GENDER as $gender) {
            $genderData[$gender] = array_fill(0, count($seasonNames), 0);
        }

        for ($i = 0; $i < count($seasonNames); $i++) {
            for ($j = 0; $j < count($totalPerSeason); $j++) {
                if ($totalPerSeason[$j]['name'] == $seasonNames[$i]) {
                    $genderData[$totalPerSeason[$j]['gender']][$i] += $totalPerSeason[$j]['total'];
                }
            }
        }

        for ($i = 0; $i < count($grandTotalPerSeason); $i++) {
            $genderData['Total'][$i] += $grandTotalPerSeason[$i]['total'];
        }

        foreach (self::GENDER as $gender => $label) {
            $genderDatataSets[] = [
                'label' => $gender,
                'backgroundColor' => self::GENDER_PALETTE[$label],
                'data' => $genderData[$label],
            ];
        }

        $genderChart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $genderChart->setData([
            'labels' => $seasonNames,
            'datasets' => $genderDatataSets
        ]);
        $genderChart->setOptions([
            "scales" => [
                "xAxes" => [
                    [
                        "stacked" => true
                    ]
                ],
                "yAxes" => [
                    [
                        "stacked" => false,
                        'ticks' => [
                            'beginAtZero' => true,
                            'max' => 500
                        ]
                    ]
                ],
            ]
        ]);

        //construction du graphique de licences
        foreach (self::LICENCES_NAME as $licenceName) {
            $licencesData[$licenceName] = array_fill(0, count($seasonNames), 0);
        }

        $totalLicences = $subscriptionRepository->totalLicencesPerSeason($categoryFilter);

        for ($i = 0; $i < count($seasonNames); $i++) {
            for ($j = 0; $j < count($totalLicences); $j++) {
                if ($totalLicences[$j]['seasonName'] == $seasonNames[$i]) {
                    $licencesData[$totalLicences[$j]['name']][$i] += $totalLicences[$j]['total'];
                }
            }
        }

        for ($i = 0; $i < count($grandTotalPerSeason); $i++) {
            $licencesData['Total'][$i] += $grandTotalPerSeason[$i]['total'];
        }

        foreach (self::LICENCES_NAME as $licenceName) {
            $licenceDatataSets[] = [
                'label' => $licenceName,
                'backgroundColor' => self::LICENCES_PALETTE[$licenceName],
                'data' => $licencesData[$licenceName],
            ];
        }

        $licencesChart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $licencesChart->setData([
            'labels' => $seasonNames,
            'datasets' => $licenceDatataSets
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
                        "stacked" => false,
                        'ticks' => [
                            'beginAtZero' => true,
                            'max' => 500
                        ]
                    ]
                ],
            ]
        ]);

        //construction du graphique de catégories
        $totalCategories = $subscriptionRepository->totalCategoriesPerSeason($licenceFilter);
        foreach (self::CATEGORIES_NAME as $categoryName => $categoryLabel) {
            $categoriesData[$categoryName] = array_fill(0, count($seasonNames), 0);
        }

        for ($i = 0; $i < count($seasonNames); $i++) {
            for ($j = 0; $j < count($totalCategories); $j++) {
                if ($totalCategories[$j]['seasonName'] == $seasonNames[$i]) {
                    foreach (self::CATEGORIES_NAME as $categoryLabel => $category) {
                        if (in_array($totalCategories[$j]['label'], self::CATEGORIES_NAME[$categoryLabel])) {
                            $categoriesData[$categoryLabel][$i] += $totalCategories[$j]['total'];
                        }
                    }
                }
            }
        }

        foreach (self::CATEGORIES_NAME as $categoryName => $labels) {
            $categoryDataSets[] = [
                'label' => $categoryName,
                'backgroundColor' => self::CATEGORIES_PALETTES[$categoryName],
                'data' => $categoriesData[$categoryName],
            ];
        }

        $categoriesChart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $categoriesChart->setData([
            'labels' => $seasonNames,
            'datasets' => $categoryDataSets
        ]);
        $categoriesChart->setOptions([
            "scales" => [
                "xAxes" => [
                    [
                        "stacked" => true
                    ]
                ],
                "yAxes" => [
                    [
                        "stacked" => false,
                        'ticks' => [
                            'beginAtZero' => true,
                            'max' => 500
                        ]
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
            'licencesChart' => $licencesChart,
            'categoriesChart' => $categoriesChart,
            'categoryFilter' => $categoryFilter,
            'licenceFilter' => $licenceFilter,
            'genderChart' => $genderChart
        ]);
    }

    /**
     * @Route("/sortants", name="outgoing")
     * @SuppressWarnings(PHPMD.LongVariable)
     * @param SeasonRepository $seasonRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param LicenceRepository $licenceRepository
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function outgoingStatistics(
        SeasonRepository $seasonRepository,
        SubscriptionRepository $subscriptionRepository,
        LicenceRepository $licenceRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $subscriptions = [];
        $categories = $categoryRepository->findAll();
        $licences = $licenceRepository->findAll();
        $seasons = $seasonRepository->findAll();

        foreach ($categories as $category) {
            foreach ($licences as $licence) {
                for ($season = 1; $season < count($seasons); $season++) {
                    $previousSub = $subscriptionRepository->findBy([
                        'season' => $seasons[$season - 1],
                        'licence' => $licence,
                        'category' => $category
                    ]);

                    $subscriptions[$category->getLabel()]
                    [$licence->getAcronym()]
                    [$seasons[$season]->getName()]['H'] = 0;
                    $subscriptions[$category->getLabel()]
                    [$licence->getAcronym()]
                    [$seasons[$season]->getName()]['F'] = 0;

                    foreach ($previousSub as $subscription) {
                        $subscriptionsSubscriber = $subscription->getSubscriber()->getSubscriptions();
                        $subscriberGender = $subscription->getSubscriber()->getGender();

                        $subscriberSeasons = [];
                        foreach ($subscriptionsSubscriber as $subscriber) {
                            $subscriberSeasons[] = $subscriber->getSeason()->getName();
                        }

                        if (!in_array($seasons[$season]->getName(), $subscriberSeasons)) {
                            $subscriptions[$category->getLabel()]
                            [$licence->getAcronym()]
                            [$seasons[$season]->getName()]
                            [$subscriberGender]++;
                        }
                    }
                }
            }
        }

        return $this->render('statistics/outgoing.html.twig', [
            'statistics' => $subscriptions,
            'seasons' => $seasons,
        ]);
    }
}
