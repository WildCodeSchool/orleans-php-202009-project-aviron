<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/statistics", name="statistics_")
 */
class StatisticsController extends AbstractController
{
    /**
     * @Route("/general", name="general")
     * @SuppressWarnings(PHPMD.LongVariable)
     * @param SeasonRepository $seasonRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param LicenceRepository $licenceRepository
     * @param CategoryRepository $categoryRepository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
//    public function generalStatistics(
//        SeasonRepository $seasonRepository,
//        SubscriptionRepository $subscriptionRepository,
//        LicenceRepository $licenceRepository,
//        CategoryRepository $categoryRepository
//    ): Response {
//        $categories = $categoryRepository->findAll();
//        $licences = $licenceRepository->findAll();
//        $seasons = $seasonRepository->findAll();
//        $i = 0;
//        foreach ($categories as $category) {
//            foreach ($licences as $licence) {
//                $subscribersPerCategoryPerLicencePerSeason[$i] =
//                    $subscriptionRepository->findSubscribersByCategoryByLicenceBySeason(
//                        $category->getLabel(),
//                        $licence->getAcronym()
//                    );
//                $i++;
//            }
//            dd($subscribersPerCategoryPerLicencePerSeason);
//        }
//    }
}
