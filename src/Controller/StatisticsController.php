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
     */
    public function generalStatistics(
        SeasonRepository $seasonRepository,
        SubscriptionRepository $subscriptionRepository,
        LicenceRepository $licenceRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $subscriptions = [];
        $categories = $categoryRepository->findAll();
        $licences = $licenceRepository->findAll();
        $seasons = $seasonRepository->findAll();

        foreach ($categories as $category) {
            foreach ($licences as $licence) {
                $subscriptions[$category->getLabel()][$licence->getAcronym()] =
                    $subscriptionRepository->findSubscriptionsBySeason(
                        $category->getLabel(),
                        $licence->getAcronym()
                    );
            }
        }

        return $this->render('statistics/general.html.twig', [
            'statistics' => $subscriptions,
            'seasons' => $seasons,
            'categories' => $categories,
            'licences' => $licences
        ]);
    }

    /**
     * @Route("/outgoing", name="outgoing")
     * @SuppressWarnings(PHPMD.LongVariable)
     * @param SeasonRepository $seasonRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param LicenceRepository $licenceRepository
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function outgoingStatistics(
        SeasonRepository $seasonRepository,
        SubscriptionRepository $subscriptionRepository,
        LicenceRepository $licenceRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $subscriptions = [];
        $categories = $categoryRepository->findAll();
        $licences = $licenceRepository->findAll();
        $seasons = $seasonRepository->findAll();

        foreach ($categories as $category) {
            foreach ($licences as $licence) {
                for ($season = 1; $season < count($seasons); $season++) {
                    $previousSub = $subscriptionRepository->findBy([
                        'season' => $seasons[$season - 1],
                        'licence' => $licence,
                        'category' => $category
                    ]);

                    $subscriptions[$category->getLabel()]
                    [$licence->getAcronym()]
                    [$seasons[$season]->getName()]['H'] = 0;
                    $subscriptions[$category->getLabel()]
                    [$licence->getAcronym()]
                    [$seasons[$season]->getName()]['F'] = 0;

                    foreach ($previousSub as $subscription) {
                        $subscriptionsSubscriber = $subscription->getSubscriber()->getSubscriptions();
                        $subscriberGender = $subscription->getSubscriber()->getGender();

                        $subscriberSeasons = [];
                        foreach ($subscriptionsSubscriber as $subscriber) {
                            $subscriberSeasons[] = $subscriber->getSeason()->getName();
                        }

                        if (!in_array($seasons[$season]->getName(), $subscriberSeasons)) {
                            $subscriptions[$category->getLabel()]
                            [$licence->getAcronym()]
                            [$seasons[$season]->getName()]
                            [$subscriberGender]++;
                        }
                    }
                }
            }
        }

        return $this->render('statistics/outgoing.html.twig', [
            'statistics' => $subscriptions,
            'seasons' => $seasons,
        ]);
    }
}
