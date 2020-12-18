<?php

namespace App\Controller;

use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriptionRepository;
use App\Service\CountSubscribers;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private const COMPETITION_LICENCE = 'A';
    private const JUNIOR_CATEGORY = 'J';

    /**
     * @Route("/", name="home")
     * @param SeasonRepository $season
     * @param SubscriptionRepository $subscription
     * @return Response
     * @throws NonUniqueResultException
     */
    public function index(
        CountSubscribers $countSubscribers,
        SeasonRepository $seasonRepo,
        SubscriptionRepository $subscriptionRepo,
        LicenceRepository $licenceRepository
    ): Response {
        $actualSeason = $seasonRepo->findOneBy([], ['name' => 'DESC'])->getName();
  
        $actualSubscribers = $subscriptionRepo->findAllSubscribersForActualSeason(
            self::COMPETITION_LICENCE,
            $actualSeason
        );
  
        $youngSubscribers = $subscriptionRepo->findAllYoungSubscribersForActualSeason(
            self::COMPETITION_LICENCE,
            $actualSeason,
            self::JUNIOR_CATEGORY
        );
  
        $subscribersLicences = $subscriptionRepo->subscribersByYearByLicences($actualSeason);

        $countByLicences = $countSubscribers->countSubscribersWithLabel(
            $subscribersLicences,
            $licenceRepository
        );

        return $this->render('home/index.html.twig', [
            'subscribersByLicences' => $countByLicences, 
            'youngSubscribers' => $youngSubscribers, 
            'actualSubscribers' => $actualSubscribers,
        ]);
    }
}
