<?php

namespace App\Service;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

abstract class ChartMaker
{
    /**
     * @var ChartBuilderInterface
     */
    protected ChartBuilderInterface $chartBuilder;

    public function __construct(
        ChartBuilderInterface $chartBuilder
    ) {
        $this->chartBuilder = $chartBuilder;
    }
}
