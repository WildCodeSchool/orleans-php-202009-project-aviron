<?php

namespace App\Controller;

use App\Entity\Season;
use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\StatusRepository;
use App\Repository\SubscriptionRepository;
use App\Service\CategoriesChartMaker;
use App\Service\LicencesChartMaker;
use App\Service\MonthlySubscriptionChartMaker;
use App\Service\SubscribersCounter;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends AbstractController
{
    private const COMPETITION_LICENCE = 'A';
    private const JUNIOR_CATEGORY = 'J';
    private const STATUS_NEW = 'N';
    private const STATUS_TRANSFER = 'T';

    /**
     * @Route("/", name="home")
     * @param SubscribersCounter $countSubscribers
     * @param SeasonRepository $seasonRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param LicenceRepository $licenceRepository
     * @param CategoryRepository $categoryRepository
     * @param StatusRepository $statusRepository
     * @param MonthlySubscriptionChartMaker $monthlySubscriptionChartMaker
     * @param CategoriesChartMaker $categoriesChartMaker
     * @param LicencesChartMaker $licencesChartMaker
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
        MonthlySubscriptionChartMaker $monthlySubscriptionChartMaker,
        CategoriesChartMaker $categoriesChartMaker,
        LicencesChartMaker $licencesChartMaker
    ): Response {

        // Si aucune saison en db, redirection automatique vers l'import
        if ($seasonRepository->findOneBy([], ['name' => 'DESC']) == null) {
            $this->addFlash('warning', 'Importez votre première saison pour accéder aux statistiques');
            return $this->redirectToRoute('tools_import');
        } else {
            $actualSeason = $seasonRepository->findOneBy([], ['name' => 'DESC'])->getName();
            $previousSeason = null;
            if ($seasonRepository->findBy([], ['name' => 'DESC'], 1, 1) != null) {
                $previousSeason = $seasonRepository->findBy([], ['name' => 'DESC'], 1, 1)[0]->getName();
            }
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
            [self::STATUS_NEW, self::STATUS_TRANSFER],
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

        // Tableaux comparaisons n-1
        if ($previousSeason !== null) {
            $subscribersLicencesPrevious = $subscriptionRepository->subscribersByYearByLicences($previousSeason);
            $countByLicencesPrevious = $countSubscribers->countSubscribersWithLabel(
                $subscribersLicencesPrevious,
                $licenceRepository
            );
            $subscribersCategoriesPrevious = $subscriptionRepository->subscribersByYearByCategories($previousSeason);
            $countByCategoriesPrevious = $countSubscribers->countSubscribersWithLabel(
                $subscribersCategoriesPrevious,
                $categoryRepository
            );
            $subscribersStatusPrevious = $subscriptionRepository->subscribersByYearByStatus($previousSeason);
            $countByStatusPrevious = $countSubscribers->countSubscribersWithLabel(
                $subscribersStatusPrevious,
                $statusRepository
            );
        }

        $monthlySubscriptionsChart = $monthlySubscriptionChartMaker->getChart($actualSeason, $previousSeason);

        $categoriesChart = $categoriesChartMaker->getChart($actualSeason);

        $licencesChart = $licencesChartMaker->getChart($actualSeason);

        return $this->render('home/index.html.twig', [
            'currentSeason' => $actualSeason,
            'previousSeason' => $previousSeason,
            'subscribersByLicences' => $countByLicences,
            'previousSubscribersByLicences' => $countByLicencesPrevious ?? [],
            'subscribersByCategories' => $countByCategories,
            'previousSubscribersByCategories' => $countByCategoriesPrevious ?? [],
            'subscribersByStatus' => $countByStatus,
            'previousSubscribersByStatus' => $countByStatusPrevious ?? [],
            'youngSubscribers' => $youngSubscribers,
            'actualSubscribers' => $actualSubscribers,
            'newSubscribers' => $newSubscribers,
            'categoriesChart' => $categoriesChart,
            'licencesChart' => $licencesChart,
            'monthlySubscriptionsChart' => $monthlySubscriptionsChart,
        ]);
    }
}
