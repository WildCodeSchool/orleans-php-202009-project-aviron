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
        "Jeunes" => ['J9', 'J10', 'J11', 'J12', 'J13', 'J14'],
        "Juniors" => ['J15', 'J16', 'J17', 'J18'],
        "Seniors" => ['S', 'S-23'],
        "Total" => [],
    ];

    private const CATEGORIES_PALETTES = [
        'Jeunes' => '#37cf9b',
        'Juniors' => '#6688c3',
        'Seniors' => '#a65bd7',
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

        $genderChart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $genderChart->setData([
            'labels' => $seasonNames,
            'datasets' => [
                [
                    'type' => 'bar',
                    'label' => 'Femme',
                    'backgroundColor' => self::GENDER_PALETTE['F'],
                    'data' => $genderData['F'],
                    'stack' => 1,
                    'barPercentage' => 1,

                ],
                [
                    'type' => 'bar',
                    'label' => 'Homme',
                    'backgroundColor' => self::GENDER_PALETTE['H'],
                    'data' => $genderData['H'],
                    'stack' => 1,
                    'barPercentage' => 1,
                ],
                [
                    'type' => 'bar',
                    'label' => 'Total',
                    'backgroundColor' => self::GENDER_PALETTE['Total'],
                    'data' => $genderData['Total'],
                    'stack' => 0,
                    'barPercentage' => 0,
                    'barThickness' => 20,
                ],
            ]
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
                        "stacked" => true,
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

        $licencesChart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $licencesChart->setData([
            'labels' => $seasonNames,
            'datasets' => [
                [
                    'type' => 'bar',
                    'label' => 'Découverte',
                    'backgroundColor' => self::LICENCES_PALETTE['Découverte'],
                    'data' => $licencesData['Découverte'],
                    'stack' => 1,
                    'barPercentage' => 1,

                ],
                [
                    'type' => 'bar',
                    'label' => 'Compétition',
                    'backgroundColor' => self::LICENCES_PALETTE['Compétition'],
                    'data' => $licencesData['Compétition'],
                    'stack' => 1,
                    'barPercentage' => 1,
                ],
                [
                    'type' => 'bar',
                    'label' => 'Universitaire',
                    'backgroundColor' => self::LICENCES_PALETTE['Universitaire'],
                    'data' => $licencesData['Universitaire'],
                    'stack' => 1,
                    'barPercentage' => 1,
                ],
                [
                    'type' => 'bar',
                    'label' => 'Indoor',
                    'backgroundColor' => self::LICENCES_PALETTE['Indoor'],
                    'data' => $licencesData['Indoor'],
                    'stack' => 1,
                    'barPercentage' => 1,
                ],
                [
                    'type' => 'bar',
                    'label' => 'Total',
                    'backgroundColor' => self::LICENCES_PALETTE['Total'],
                    'data' => $licencesData['Total'],
                    'stack' => 0,
                    'barPercentage' => 0,
                    'barThickness' => 20,
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
                        "stacked" => true,
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

        $categoriesData['Total'] = array_fill(0, count($seasonNames), 0);

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

        for ($i = 0; $i < count($grandTotalPerSeason); $i++) {
            $categoriesData['Total'][$i] += $grandTotalPerSeason[$i]['total'];
        }

        $categoriesChart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $categoriesChart->setData([
            'labels' => $seasonNames,
            'datasets' => [
                [
                    'type' => 'bar',
                    'label' => 'Jeune',
                    'backgroundColor' => self::CATEGORIES_PALETTES['Jeunes'],
                    'data' => $categoriesData['Jeunes'],
                    'stack' => 1,
                    'barPercentage' => 1,

                ],
                [
                    'type' => 'bar',
                    'label' => 'Junior',
                    'backgroundColor' => self::CATEGORIES_PALETTES['Juniors'],
                    'data' => $categoriesData['Juniors'],
                    'stack' => 1,
                    'barPercentage' => 1,
                ],
                [
                    'type' => 'bar',
                    'label' => 'Senior',
                    'backgroundColor' => self::CATEGORIES_PALETTES['Seniors'],
                    'data' => $categoriesData['Seniors'],
                    'stack' => 1,
                    'barPercentage' => 1,
                ],
                [
                    'type' => 'bar',
                    'label' => 'Total',
                    'backgroundColor' => self::LICENCES_PALETTE['Total'],
                    'data' => $categoriesData['Total'],
                    'stack' => 0,
                    'barPercentage' => 0,
                    'barThickness' => 20,
                ],
            ]
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
                        "stacked" => true,
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
     * @param ChartBuilderInterface $chartBuilder
     * @return Response
     */
    public function outgoingStatistics(
        SeasonRepository $seasonRepository,
        SubscriptionRepository $subscriptionRepository,
        LicenceRepository $licenceRepository,
        CategoryRepository $categoryRepository,
        ChartBuilderInterface $chartBuilder
    ): Response {
        $subscriptions = [];
        $categories = $categoryRepository->findAll();
        $licences = $licenceRepository->findAll();
        $seasons = $seasonRepository->findBy([], ['name' => 'ASC']);

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

        //getting total outgoing members by gender
        $subscriptionsGenders = [];

        for ($season = 1; $season < count($seasons); $season++) {
            $previousSub = $subscriptionRepository->findBy([
                'season' => $seasons[$season - 1],
            ]);

            $subscriptionsGenders[$seasons[$season]->getName()]['H'] = 0;
            $subscriptionsGenders[$seasons[$season]->getName()]['F'] = 0;

            foreach ($previousSub as $subscription) {
                $subscriptionsSubscriber = $subscription->getSubscriber()->getSubscriptions();
                $subscriberGender = $subscription->getSubscriber()->getGender();

                $subscriberSeasons = [];
                foreach ($subscriptionsSubscriber as $subscriber) {
                    $subscriberSeasons[] = $subscriber->getSeason()->getName();
                }

                if (!in_array($seasons[$season]->getName(), $subscriberSeasons)) {
                    $subscriptionsGenders[$seasons[$season]->getName()]
                    [$subscriberGender]++;
                }
            }
        }

        $seasonNames = [];
        for ($i = 1; $i < count($seasons); $i++) {
            $seasonNames[] = $seasons[$i]->getName();
        }

        $outgoingGenderData = [];

        foreach (self::GENDER as $gender) {
            $outgoingGenderData[$gender] = array_fill(0, count($seasonNames), 0);
        }

        for ($i = 0; $i < count($seasonNames); $i++) {
            $outgoingGenderData['H'][$i] = $subscriptionsGenders[$seasonNames[$i]]['H'];
            $outgoingGenderData['F'][$i] = $subscriptionsGenders[$seasonNames[$i]]['F'];
            $outgoingGenderData['Total'][$i] =
                $subscriptionsGenders[$seasonNames[$i]]['H'] + $subscriptionsGenders[$seasonNames[$i]]['F'];
        }

        foreach ($subscriptionsGenders as $seasonsData) {
            foreach ($seasonsData as $gender => $total) {
                $outgoingGenderData[] = [$gender => $total];
            }
        }

        $outgoingGenderChart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $outgoingGenderChart->setData([
            'labels' => $seasonNames,
            'datasets' => [
                [
                    'type' => 'bar',
                    'label' => 'Femme',
                    'backgroundColor' => self::GENDER_PALETTE['F'],
                    'data' => $outgoingGenderData['F'],
                    'stack' => 1,
                    'barPercentage' => 1,

                ],
                [
                    'type' => 'bar',
                    'label' => 'Homme',
                    'backgroundColor' => self::GENDER_PALETTE['H'],
                    'data' => $outgoingGenderData['H'],
                    'stack' => 1,
                    'barPercentage' => 1,
                ],
                [
                    'type' => 'bar',
                    'label' => 'Total',
                    'backgroundColor' => self::GENDER_PALETTE['Total'],
                    'data' => $outgoingGenderData['Total'],
                    'stack' => 0,
                    'barPercentage' => 0,
                    'barThickness' => 20,
                ],
            ]
        ]);

        $outgoingGenderChart->setOptions([
            "scales" => [
                "xAxes" => [
                    [
                        "stacked" => true
                    ]
                ],
                "yAxes" => [
                    [
                        "stacked" => true,
                        'ticks' => [
                            'beginAtZero' => true,
                            'max' => 500
                        ]
                    ]
                ],
            ]
        ]);

        //getting total outgoing members by licences
        $subscriptionsLicences = [];

        foreach ($licences as $licence) {
            for ($season = 1; $season < count($seasons); $season++) {
                $previousSub = $subscriptionRepository->findBy([
                    'season' => $seasons[$season - 1],
                    'licence' => $licence,
                ]);

                if (!isset($subscriptionsLicences[$licence->getName()][$seasons[$season]->getName()])) {
                    $subscriptionsLicences[$licence->getName()][$seasons[$season]->getName()] = 0;
                }

                foreach ($previousSub as $subscription) {
                    $subscriptionsSubscriber = $subscription->getSubscriber()->getSubscriptions();

                    $subscriberSeasons = [];
                    foreach ($subscriptionsSubscriber as $subscriber) {
                        $subscriberSeasons[] = $subscriber->getSeason()->getName();
                    }
                    if (!in_array($seasons[$season]->getName(), $subscriberSeasons)) {
                        $subscriptionsLicences[$licence->getName()]
                        [$seasons[$season]->getName()]++;
                    }
                }
            }
        }

        $licenceNames = [];

        for ($i = 0; $i < count($licences); $i++) {
            $licenceNames[] = $licences[$i]->getName();
        }

        $licenceNames = array_values(array_unique($licenceNames));

        foreach (self::LICENCES_NAME as $licenceName) {
            $licencesData[$licenceName] = array_fill(0, count($seasonNames), 0);
        }

        for ($i = 0; $i < count($licenceNames); $i++) {
            for ($j = 0; $j < count($seasonNames); $j++) {
                $licencesData[$licenceNames[$i]][$j] = $subscriptionsLicences[$licenceNames[$i]][$seasonNames[$j]];
                $licencesData['Total'][$j] += $subscriptionsLicences[$licenceNames[$i]][$seasonNames[$j]];
            }
        }

        $outgoingLicenceChart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $outgoingLicenceChart->setData([
            'labels' => $seasonNames,
            'datasets' => [
                [
                    'type' => 'bar',
                    'label' => 'Découverte',
                    'backgroundColor' => self::LICENCES_PALETTE['Découverte'],
                    'data' => $licencesData['Découverte'],
                    'stack' => 1,
                    'barPercentage' => 1,

                ],
                [
                    'type' => 'bar',
                    'label' => 'Compétition',
                    'backgroundColor' => self::LICENCES_PALETTE['Compétition'],
                    'data' => $licencesData['Compétition'],
                    'stack' => 1,
                    'barPercentage' => 1,
                ],
                [
                    'type' => 'bar',
                    'label' => 'Universitaire',
                    'backgroundColor' => self::LICENCES_PALETTE['Universitaire'],
                    'data' => $licencesData['Universitaire'],
                    'stack' => 1,
                    'barPercentage' => 1,
                ],
                [
                    'type' => 'bar',
                    'label' => 'Indoor',
                    'backgroundColor' => self::LICENCES_PALETTE['Indoor'],
                    'data' => $licencesData['Indoor'],
                    'stack' => 1,
                    'barPercentage' => 1,
                ],
                [
                    'type' => 'bar',
                    'label' => 'Total',
                    'backgroundColor' => self::LICENCES_PALETTE['Total'],
                    'data' => $licencesData['Total'],
                    'stack' => 0,
                    'barPercentage' => 0,
                    'barThickness' => 20,
                ],
            ]
        ]);
        $outgoingLicenceChart->setOptions([
            "scales" => [
                "xAxes" => [
                    [
                        "stacked" => true
                    ]
                ],
                "yAxes" => [
                    [
                        "stacked" => true,
                        'ticks' => [
                            'beginAtZero' => true,
                            'max' => 500
                        ]
                    ]
                ],
            ]
        ]);

        //getting total outgoing members by categories
        $subscriptionsCategories = [];

        foreach ($categories as $category) {
            for ($season = 1; $season < count($seasons); $season++) {
                $previousSub = $subscriptionRepository->findBy([
                    'season' => $seasons[$season - 1],
                    'category' => $category,
                ]);

                if (!isset($subscriptionsCategories[$category->getNewGroup()][$seasons[$season]->getName()])) {
                    $subscriptionsCategories[$category->getNewGroup()][$seasons[$season]->getName()] = 0;
                }

                foreach ($previousSub as $subscription) {
                    $subscriptionsSubscriber = $subscription->getSubscriber()->getSubscriptions();

                    $subscriberSeasons = [];
                    foreach ($subscriptionsSubscriber as $subscriber) {
                        $subscriberSeasons[] = $subscriber->getSeason()->getName();
                    }
                    if (!in_array($seasons[$season]->getName(), $subscriberSeasons)) {
                        $subscriptionsCategories[$category->getNewGroup()]
                        [$seasons[$season]->getName()]++;
                    }
                }
            }
        }

        $categoryNames = [];

        for ($i = 0; $i < count($categories); $i++) {
            $categoryNames[] = $categories[$i]->getNewGroup();
        }

        $categoryNames = array_values(array_unique($categoryNames));

        $categoryData = [];

        foreach (self::CATEGORIES_NAME as $categoryName => $saucisse) {
            $categoryData[$categoryName] = array_fill(0, count($seasonNames), 0);
        }
        $categoryData['Total'] = array_fill(0, count($seasonNames), 0);

        for ($i = 0; $i < count($categoryNames); $i++) {
            for ($j = 0; $j < count($seasonNames); $j++) {
                $categoryData[$categoryNames[$i]][$j] = $subscriptionsCategories[$categoryNames[$i]][$seasonNames[$j]];
                $categoryData['Total'][$j] += $subscriptionsCategories[$categoryNames[$i]][$seasonNames[$j]];
            }
        }

        $outgoingCategoryChart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $outgoingCategoryChart->setData([
            'labels' => $seasonNames,
            'datasets' => [
                [
                    'type' => 'bar',
                    'label' => 'Jeune',
                    'backgroundColor' => self::CATEGORIES_PALETTES['Jeunes'],
                    'data' => $categoryData['Jeunes'],
                    'stack' => 1,
                    'barPercentage' => 1,

                ],
                [
                    'type' => 'bar',
                    'label' => 'Junior',
                    'backgroundColor' => self::CATEGORIES_PALETTES['Juniors'],
                    'data' => $categoryData['Juniors'],
                    'stack' => 1,
                    'barPercentage' => 1,
                ],
                [
                    'type' => 'bar',
                    'label' => 'Senior',
                    'backgroundColor' => self::CATEGORIES_PALETTES['Seniors'],
                    'data' => $categoryData['Seniors'],
                    'stack' => 1,
                    'barPercentage' => 1,
                ],
                [
                    'type' => 'bar',
                    'label' => 'Total',
                    'backgroundColor' => self::LICENCES_PALETTE['Total'],
                    'data' => $categoryData['Total'],
                    'stack' => 0,
                    'barPercentage' => 0,
                    'barThickness' => 20,
                ],
            ]
        ]);
        $outgoingCategoryChart->setOptions([
            "scales" => [
                "xAxes" => [
                    [
                        "stacked" => true
                    ]
                ],
                "yAxes" => [
                    [
                        "stacked" => true,
                        'ticks' => [
                            'beginAtZero' => true,
                            'max' => 500
                        ]
                    ]
                ],
            ]
        ]);


        return $this->render('statistics/outgoing.html.twig', [
            'statistics' => $subscriptions,
            'seasons' => $seasons,
            'outgoingGenderChart' => $outgoingGenderChart,
            'outgoingLicenceChart' => $outgoingLicenceChart,
            'outgoingCategoryChart' => $outgoingCategoryChart,
        ]);
    }
}
