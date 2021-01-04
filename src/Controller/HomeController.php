<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriptionRepository;
use App\Service\SubscribersCounter;
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
     * @param SubscribersCounter $countSubscribers
     * @param SeasonRepository $seasonRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param LicenceRepository $licenceRepository
     * @param CategoryRepository $categoryRepository
     * @return Response
     * @throws NonUniqueResultException
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function index(
        SubscribersCounter $countSubscribers,
        SeasonRepository $seasonRepository,
        SubscriptionRepository $subscriptionRepository,
        LicenceRepository $licenceRepository,
        CategoryRepository $categoryRepository
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

        return $this->render('home/index.html.twig', [
            'subscribersByLicences' => $countByLicences,
            'subscribersByCategories' => $countByCategories,
            'youngSubscribers' => $youngSubscribers,
            'actualSubscribers' => $actualSubscribers,
        ]);
    }
}
