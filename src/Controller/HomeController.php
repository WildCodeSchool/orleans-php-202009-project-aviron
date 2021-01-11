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
     * @param SubscribersCounter $countSubscribers
     * @param SeasonRepository $seasonRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param LicenceRepository $licenceRepository
     * @param CategoryRepository $categoryRepository
     * @param Builder $categoriesBuilder
     * @param Builder $licencesBuilder
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
        Builder $categoriesBuilder,
        Builder $licencesBuilder
    ): Response {

        // Si aucune saison en db, redirection automatique vers l'import
        if ($seasonRepository->findOneBy([], ['name' => 'DESC']) == null) {
            $this->addFlash('warning', 'Importez votre première saison pour accéder aux statistiques');
            return $this->redirectToRoute('tools_import');
        } else {
            $actualSeason = $seasonRepository->findOneBy([], ['name' => 'DESC'])->getName();
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

        $categoriesBuilder
            ->query($querySubscribersCategories)
            ->addDataSet('subscribersCount', 'Subscribers', [
                "backgroundColor" => self::CATEGORIES_PALETTE
            ])
            ->labels('label');
        $categoriesChart = $categoriesBuilder->buildChart('categories-chart', Chart::DOUGHNUT);
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
        $licencesChart = $licencesBuilder->buildChart('licences-chart', Chart::DOUGHNUT);
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
            'youngSubscribers' => $youngSubscribers,
            'actualSubscribers' => $actualSubscribers,
            'categoriesChart' => $categoriesChart,
            'licencesChart' => $licencesChart,
        ]);
    }
}
