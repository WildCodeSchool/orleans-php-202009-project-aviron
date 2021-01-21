<?php

namespace App\Service;

use App\Repository\SubscriptionRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ChartMaker
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

    /**
     * @var SubscriptionRepository
     */
    private SubscriptionRepository $subscriptionRepository;
    /**
     * @var ChartBuilderInterface
     */
    private ChartBuilderInterface $chartBuilder;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        ChartBuilderInterface $chartBuilder
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->chartBuilder = $chartBuilder;
    }

    public function getMonthlySubscriptionsChart(?string $currentSeason, ?string $previousSeason): Chart
    {
        $currentSeasonMonthlyCount = $this->subscriptionRepository->getMonthlySubscriptionsByYear($currentSeason);
        $previousSeasonMonthlyCount = $this->subscriptionRepository->getMonthlySubscriptionsByYear($previousSeason);

        // Mise en forme des données récupérées pour les passer au graphique
        $currentSeasonData = [];
        $previousSeasonData = [];
        for ($i = 0; $i < 12; $i++) {
            if ($currentSeasonMonthlyCount[$i]['month'] == self::MONTH_SORT[$i]) {
                $currentSeasonData[] = $currentSeasonMonthlyCount[$i]['count'];
            } else {
                $currentSeasonData[] = 0;
            }

            if ($previousSeasonMonthlyCount[$i]['month'] == self::MONTH_SORT[$i]) {
                $previousSeasonData[] = $previousSeasonMonthlyCount[$i]['count'];
            } else {
                $previousSeasonData[] = 0;
            }
        }

        $monthlySubscriptionsChart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $monthlySubscriptionsChart->setData([
            'labels' => self::MONTH_LABELS,
            'datasets' => [
                [
                    'label' => 'Saison en cours',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'data' => $currentSeasonData,
                ],
                [
                    'label' => 'Saison précédente',
                    'backgroundColor' => 'rgb(12, 99, 132)',
                    'data' => $previousSeasonData,
                ],
            ],
        ]);

        return $monthlySubscriptionsChart;
    }
}
