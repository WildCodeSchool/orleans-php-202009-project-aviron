<?php

namespace App\Service;

use App\Repository\SubscriptionRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class LicencesChartMaker extends ChartMaker
{
    private const LICENCES_PALETTE = [
        '#F6246A',
        '#F87380',
        '#F99A8A',
        '#FBE9A0',
    ];

    private const LICENCES_GROUPS = [
        0 => ['A'],
        1 => ['U'],
        2 => ['D7', 'D90', 'D30'],
        3 => ['I'],
    ];

    private const LICENCES_LABELS = ['A', 'U', 'D', 'I'];

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

    public function getChart(?string $season): Chart
    {
        $subscriptionsByLicences = $this->subscriptionRepository->subscribersByYearByLicences($season);
        $subscriptionsData = [0, 0, 0, 0];

        foreach ($subscriptionsByLicences as $subscriptionsByLicence) {
            foreach (self::LICENCES_GROUPS as $index => $labels) {
                if (in_array($subscriptionsByLicence['label'], $labels)) {
                    $subscriptionsData[$index] += $subscriptionsByLicence['subscribersCount'];
                }
            }
        }

        $categoriesChart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $categoriesChart->setData([
            'labels' => self::LICENCES_LABELS,
            'datasets' => [
                [
                    'backgroundColor' => self::LICENCES_PALETTE,
                    'data' => $subscriptionsData,
                ],
            ]
        ]);

        $categoriesChart->setOptions([
            'legend' => ([
                'position' => 'bottom',
            ]),

        ]);

        return $categoriesChart;
    }
}
