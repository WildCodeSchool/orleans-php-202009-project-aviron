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
        $currentSeasonSubscribers = '';
        $previousSeasonSubscribers = '';

        $monthlySubscriptionsChart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $monthlySubscriptionsChart->setData([
            'labels' => self::MONTH_LABELS,
            'datasets' => [
                [
                    'label' => 'Saison en cours',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'data' => $currentSeasonSubscribers,
                ],
                [
                    'label' => 'Saison précédente',
                    'backgroundColor' => 'rgb(12, 99, 132)',
                    'data' => $previousSeasonSubscribers,
                ],
            ],
        ]);

        return $monthlySubscriptionsChart;
    }
}
