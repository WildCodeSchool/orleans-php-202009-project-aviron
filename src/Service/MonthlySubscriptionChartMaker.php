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
        $currentSeasonMonthlyCountRaw = $this
            ->subscriptionRepository
            ->getMonthlySubscriptionsByYear($currentSeason);
        $previousSeasonMonthlyCountRaw = $this
            ->subscriptionRepository
            ->getMonthlySubscriptionsByYear($previousSeason);

        // Tri des données de septembre à aout
        $currentSeasonMonthlyCount = $this->seasonMonthlySort($currentSeasonMonthlyCountRaw);
        $previousSeasonMonthlyCount = $this->seasonMonthlySort($previousSeasonMonthlyCountRaw);

        // Mise en forme des données récupérées pour les passer au graphique
        $currentSeasonData = [];
        $previousSeasonData = [];
        $currentSeasonIndex = 0;
        $previousSeasonIndex = 0;
        $totalCurrent = 0;
        $totalPrevious = 0;
        $lastMonthInFileCurrent = false;
        $lastMonthInFilePrevious = false;

        for ($i = 0; $i < 12; $i++) {
            if (!$lastMonthInFileCurrent) {
                if (
                    isset($currentSeasonMonthlyCount[$currentSeasonIndex]['month']) &&
                    $currentSeasonMonthlyCount[$currentSeasonIndex]['month'] == self::MONTH_SORT[$i]
                ) {
                    $totalCurrent += $currentSeasonMonthlyCount[$currentSeasonIndex]['count'];
                    $currentSeasonData[] = $totalCurrent;

                    $lastMonthInFileCurrent = $this->isLastSeasonInFile(
                        $currentSeasonMonthlyCount,
                        $currentSeasonIndex
                    );
                    $currentSeasonIndex++;
                } else {
                    $currentSeasonData[] = $totalCurrent;
                }
            }

            if (!$lastMonthInFilePrevious) {
                if (
                    isset($previousSeasonMonthlyCount[$previousSeasonIndex]['month']) &&
                    $previousSeasonMonthlyCount[$previousSeasonIndex]['month'] == self::MONTH_SORT[$i]
                ) {
                    $totalPrevious += $previousSeasonMonthlyCount[$previousSeasonIndex]['count'];
                    $previousSeasonData[] = $totalPrevious;

                    $lastMonthInFilePrevious = $this->isLastSeasonInFile(
                        $previousSeasonMonthlyCount,
                        $previousSeasonIndex
                    );

                    $previousSeasonIndex++;
                } else {
                    $previousSeasonData[] = $totalPrevious;
                }
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

    private function seasonMonthlySort(array $seasonMonthlyCount): array
    {
        $sortedMonthlyCount = [];
        for ($i = 0; $i < 12; $i++) {
            for ($j = 0; $j < 12; $j++) {
                if (
                    isset($seasonMonthlyCount[$j])
                    && (string)self::MONTH_SORT[$i] === $seasonMonthlyCount[$j]['month']
                ) {
                    $sortedMonthlyCount[] = $seasonMonthlyCount[$j];
                }
            }
        }
        return $sortedMonthlyCount;
    }

    private function isLastSeasonInFile(array $seasonMonthlyCount, int $seasonIndex): bool
    {
        return ($seasonMonthlyCount[$seasonIndex]['month'] ===
            $seasonMonthlyCount[array_key_last($seasonMonthlyCount)]['month']);
    }
}
