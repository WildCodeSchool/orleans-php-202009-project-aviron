<?php

namespace App\Controller;

use App\Entity\Subscription;
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

    /**
     * @Route("/", name="home")
     * @param SubscribersCounter $countSubscribers
     * @param SeasonRepository $seasonRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param LicenceRepository $licenceRepository
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


        $builder
            ->query('SELECT COUNT(sub.category) as subscribersCount, c.label as label 
                    FROM \App\Entity\Subscription sub
                    JOIN \App\Entity\Category c
                    WITH c.id = sub.category
                    JOIN \App\Entity\Season s
                    WITH s.id = sub.season
                    WHERE s.name = \'2018-2019\'
                    GROUP BY sub.category
                    ORDER BY c.id ASC')
            ->addDataSet('subscribersCount', 'Subscribers', [
                "backgroundColor" => RandomColorFactory::getRandomRGBAColors(12)
            ])
            ->labels('label')
        ;
        $chart = $builder->buildChart('my_chart', Chart::DOUGHNUT);
        $chart->pushOptions([
            'legend' => ([
                'position' => 'bottom',
            ]),
            'yAxes' => ([
                'gridLines' => ([
                    'drawBorder' => 'false',
                ]),
            ]),
            'xAxes' => ([
                'gridLines' => ([
                    'display' => 'false'
                    ]),
            ]),
        ]);

        return $this->render('home/index.html.twig', [
            'subscribersByLicences' => $countByLicences,
            'youngSubscribers' => $youngSubscribers,
            'actualSubscribers' => $actualSubscribers,
            'chart' => $chart,
/*            'query' => $query,*/
        ]);
    }
}
