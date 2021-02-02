<?php

namespace App\Service;

use App\Repository\SubscriptionRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class MonthlySubscriptionChartMaker extends ChartMaker
{
    private const MONTH_LABELS = [
        'Septembre',
        'Octobre',
        'Novembre',
        'Décembre',
        'Janvier',
        'Février',
        'Mars',
        'Avril',
        'Mai',
        'Juin',
        'Juillet',
        'Aout'
    ];

    private const MONTH_SORT = [9, 10, 11, 12, 1, 2, 3, 4, 5, 6, 7, 8];

    private const CURRENT_YEAR_COLOR = '#05445e';
    private const PREVIOUS_YEAR_COLOR = '#82B2C2';

    /**
     * @var SubscriptionRepository
     */
    private SubscriptionRepository $subscriptionRepository;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        ChartBuilderInterface $chartBuilder
    ) {
        parent::__construct($chartBuilder);
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function getChart(?string $currentSeason, ?string $previousSeason): Chart
    {
        $currentSeasonMonthlyCount = $this->subscriptionRepository->getMonthlySubscriptionsByYear($currentSeason);
        $previousSeasonMonthlyCount = $this->subscriptionRepository->getMonthlySubscriptionsByYear($previousSeason);

        // Mise en forme des données récupérées pour les passer au graphique
        $currentSeasonData = [];
        $previousSeasonData = [];
        $currentSeasonIndex = 0;
        $previousSeasonIndex = 0;
        $totalCurrent = 0;
        $totalPrevious = 0;
        for ($i = 0; $i < 12; $i++) {
            if (
                isset($currentSeasonMonthlyCount[$currentSeasonIndex]['month']) &&
                $currentSeasonMonthlyCount[$currentSeasonIndex]['month'] == self::MONTH_SORT[$i]
            ) {
                $totalCurrent += $currentSeasonMonthlyCount[$currentSeasonIndex]['count'];
                $currentSeasonData[] = $totalCurrent;
                $currentSeasonIndex++;
            } else {
                $currentSeasonData[] = $totalCurrent;
            }

            if (
                isset($previousSeasonMonthlyCount[$previousSeasonIndex]['month']) &&
                $previousSeasonMonthlyCount[$previousSeasonIndex]['month'] == self::MONTH_SORT[$i]
            ) {
                $totalPrevious += $previousSeasonMonthlyCount[$previousSeasonIndex]['count'];
                $previousSeasonData[] = $totalPrevious;
                $previousSeasonIndex++;
            } else {
                $previousSeasonData[] = 0;
            }
        }

        $monthlySubscriptionsChart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $monthlySubscriptionsChart->setData([
            'labels' => self::MONTH_LABELS,
            'datasets' => [
                [
                    'label' => 'Saison en cours',
                    'borderColor' => self::CURRENT_YEAR_COLOR,
                    'backgroundColor' => 'transparent',
                    'data' => $currentSeasonData,
                    'lineTension' => 0,
                    'borderWidth' => 5,
                ],
                [
                    'label' => 'Saison précédente',
                    'borderColor' => self::PREVIOUS_YEAR_COLOR,
                    'backgroundColor' => 'transparent',
                    'data' => $previousSeasonData,
                    'lineTension' => 0,
                    'borderWidth' => 3,
                ],
            ],
        ]);
        $monthlySubscriptionsChart->setOptions([
            'scales' => [
                'yAxes' => [[
                    'ticks' => [
                        'beginAtZero' => true,
                    ]
                ]]
            ]

        ]);

        return $monthlySubscriptionsChart;
    }
}
