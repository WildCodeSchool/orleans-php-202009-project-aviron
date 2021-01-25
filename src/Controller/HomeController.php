<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\StatusRepository;
use App\Repository\SubscriptionRepository;
use App\Service\ChartMaker;
use App\Service\MonthlySubscriptionChart;
use App\Service\MonthlySubscriptionChartMaker;
use App\Service\SubscribersCounter;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Mukadi\ChartJSBundle\Chart\Builder;
use Mukadi\Chart\Chart as MChart;

class HomeController extends AbstractController
{
    private const COMPETITION_LICENCE = 'A';
    private const JUNIOR_CATEGORY = 'J';
    private const STATUS_NEW = 'N';
    private const STATUS_TRANSFER = 'T';
    private const CATEGORIES_PALETTE = [
        '#004C6D',
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
    private const LICENCES_PALETTE = [
        '#F6246A',
        '#F74B75',
        '#F87380',
        '#F99A8A',
        '#FAC295',
        '#FBE9A0',
    ];

    /**
     * @Route("/", name="home")
     * @SuppressWarnings(PHPMD)
     * @param SubscribersCounter $countSubscribers
     * @param SeasonRepository $seasonRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param LicenceRepository $licenceRepository
     * @param CategoryRepository $categoryRepository
     * @param StatusRepository $statusRepository
     * @param Builder $categoriesBuilder
     * @param Builder $licencesBuilder
     * @param MonthlySubscriptionChartMaker $monthlySubscriptionChartMaker
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
        StatusRepository $statusRepository,
        Builder $categoriesBuilder,
        Builder $licencesBuilder,
        MonthlySubscriptionChartMaker $monthlySubscriptionChartMaker
    ): Response {

        // Si aucune saison en db, redirection automatique vers l'import
        if ($seasonRepository->findOneBy([], ['name' => 'DESC']) == null) {
            $this->addFlash('warning', 'Importez votre première saison pour accéder aux statistiques');
            return $this->redirectToRoute('tools_import');
        } else {
            $actualSeason = $seasonRepository->findOneBy([], ['name' => 'DESC'])->getName();
            $previousSeason = $seasonRepository->findBy([], ['name' => 'DESC'], 1, 1)[0]->getName() ?? null;
        }

        $actualSubscribers = $subscriptionRepository->findAllSubscribersForActualSeason(
            self::COMPETITION_LICENCE,
            $actualSeason
        );

        $youngSubscribers = $subscriptionRepository->findAllYoungSubscribersForActualSeason(
            self::COMPETITION_LICENCE,
            $actualSeason,
            self::JUNIOR_CATEGORY
        );

        $newSubscribers = $subscriptionRepository->findAllSubscribersForSeasonByLicenceByStatus(
            self::STATUS_NEW,
            self::STATUS_TRANSFER,
            $actualSeason,
            self::COMPETITION_LICENCE,
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

        $subscribersStatus = $subscriptionRepository->subscribersByYearByStatus($actualSeason);
        $countByStatus = $countSubscribers->countSubscribersWithLabel(
            $subscribersStatus,
            $statusRepository
        );

        $monthlySubscriptionsChart = $monthlySubscriptionChartMaker->getChart($actualSeason, $previousSeason);

        $querySubscribersCategories = $subscriptionRepository->getQueryForSubscribersByYearByCategories($actualSeason);

        $categoriesBuilder
            ->query($querySubscribersCategories)
            ->addDataSet('subscribersCount', 'Subscribers', [
                "backgroundColor" => self::CATEGORIES_PALETTE
            ])
            ->labels('label');
        $categoriesChart = $categoriesBuilder->buildChart('categories-chart', MChart::DOUGHNUT);
        $categoriesChart->pushOptions([
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

        $querySubscribersLicences = $subscriptionRepository->getQueryForSubscribersByYearByLicences($actualSeason);

        $licencesBuilder
            ->query($querySubscribersLicences)
            ->addDataSet('subscribersCount', 'Subscribers', [
                "backgroundColor" => self::LICENCES_PALETTE
            ])
            ->labels('label');
        $licencesChart = $licencesBuilder->buildChart('licences-chart', MChart::DOUGHNUT);
        $licencesChart->pushOptions([
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
            'currentSeason' => $actualSeason,
            'subscribersByLicences' => $countByLicences,
            'subscribersByCategories' => $countByCategories,
            'subscribersByStatus' => $countByStatus,
            'youngSubscribers' => $youngSubscribers,
            'actualSubscribers' => $actualSubscribers,
            'newSubscribers' => $newSubscribers,
            'categoriesChart' => $categoriesChart,
            'licencesChart' => $licencesChart,
            'monthlySubscriptionsChart' => $monthlySubscriptionsChart,
        ]);
    }
}
