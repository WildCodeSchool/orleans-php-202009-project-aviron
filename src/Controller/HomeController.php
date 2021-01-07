<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriptionRepository;
use App\Service\SubscribersCounter;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Mukadi\Chart\Utils\RandomColorFactory;
use Mukadi\ChartJSBundle\Chart\Builder;
use Mukadi\Chart\Chart;

class HomeController extends AbstractController
{
    private const COMPETITION_LICENCE = 'A';
    private const JUNIOR_CATEGORY = 'J';
    private const CATEGORIES_PALETTE = ['#004C6D',
        '#135B79',
        '#256985',
        '#387892',
        '#4A869E',
        '#5D95AA',
        '#70A3B6',
        '#82B2C2',
        '#95C0CE',
        '#A7CFDB',
        '#BADDE7',
        '#CCECF3',
    ];

    /**
     * @Route("/", name="home")
     * @param SubscribersCounter $countSubscribers
     * @param SeasonRepository $seasonRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param LicenceRepository $licenceRepository
     * @param CategoryRepository $categoryRepository
     * @param Builder $builder
     * @return Response
     * @throws NonUniqueResultException
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function index(
        SubscribersCounter $countSubscribers,
        SeasonRepository $seasonRepository,
        SubscriptionRepository $subscriptionRepository,
        LicenceRepository $licenceRepository,
        CategoryRepository $categoryRepository,
        Builder $builder
    ): Response {
        $actualSeason = $seasonRepository->findOneBy([], ['name' => 'DESC'])->getName();

        $actualSubscribers = $subscriptionRepository->findAllSubscribersForActualSeason(
            self::COMPETITION_LICENCE,
            $actualSeason
        );

        $youngSubscribers = $subscriptionRepository->findAllYoungSubscribersForActualSeason(
            self::COMPETITION_LICENCE,
            $actualSeason,
            self::JUNIOR_CATEGORY
        );

        $subscribersLicences = $subscriptionRepository->subscribersByYearByLicences($actualSeason);
        $countByLicences = $countSubscribers->countSubscribersWithLabel(
            $subscribersLicences,
            $licenceRepository
        );

        $subscribersCategories = $subscriptionRepository->subscribersByYearByCategories($actualSeason);
        $countByCategories = $countSubscribers->countSubscribersWithLabel(
            $subscribersCategories,
            $categoryRepository
        );

        $querySubscribersCategories = $subscriptionRepository->getQueryForSubscribersByYearByCategories($actualSeason);

        $builder
            ->query($querySubscribersCategories)
            ->addDataSet('subscribersCount', 'Subscribers', [
                "backgroundColor" => self::CATEGORIES_PALETTE
            ])
            ->labels('label')
        ;
        $chart = $builder->buildChart('my_chart', Chart::DOUGHNUT);
        $chart->pushOptions([
            'legend' => ([
                'position' => 'bottom',
            ]),
            'scales' => ([
                'xAxes' => ([
                    'gridLines' => ([
                        'display' => 'false'
                    ])
                ])
            ])
        ]);

        return $this->render('home/index.html.twig', [
            'subscribersByLicences' => $countByLicences,
            'subscribersByCategories' => $countByCategories,
            'youngSubscribers' => $youngSubscribers,
            'actualSubscribers' => $actualSubscribers,
            'chart' => $chart,
        ]);
    }
}
