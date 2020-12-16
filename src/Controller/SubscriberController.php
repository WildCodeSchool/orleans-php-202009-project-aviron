<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriberRepository;
use App\Service\StatusCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/subscribers", name="subscribers_")
 */
class SubscriberController extends AbstractController
{
    /**
     * Correspond à la route /subscribers/ et au name "subscriber_index"
     * @Route("/{display}", methods={"GET"}, name="index")
     * @param string $display
     * @param LicenceRepository $licenceRepository
     * @param SubscriberRepository $subscriberRepository
     * @param SeasonRepository $seasonRepository
     * @param StatusCalculator $statusCalculator
     * @param CategoryRepository $categoryRepository
     * @return Response A response instance
     */
    public function index(
        string $display,
        LicenceRepository $licenceRepository,
        SubscriberRepository $subscriberRepository,
        SeasonRepository $seasonRepository,
        StatusCalculator $statusCalculator,
        CategoryRepository $categoryRepository
    ): Response {
        $licences = $licenceRepository->findAll();
        $subscribers = $subscriberRepository->findAll();
        $seasons = $seasonRepository->findAll();
        $categories = $categoryRepository->findAll();

        $previousSeason = [];
        $currentSeason = [];
        foreach ($seasons as $season) {
            foreach ($season->getSubscriptions() as $subscriptionSeason) {
                asort($previousSeason);
                $subscriptionSeason->setStatus($statusCalculator->calculateNew($subscriptionSeason, $previousSeason));
                $currentSeason[] = $subscriptionSeason->getSubscriber()->getLicenceNumber();
            }
            $previousSeason = [];
            $previousSeason = $currentSeason;
            $currentSeason = [];
        }
        foreach ($subscribers as $subscriber) {
            foreach ($subscriber->getSubscriptions() as $subscription) {
                if ($subscription->getSeason() !== $seasons[0]) {
                    $subscription->setStatus($statusCalculator->calculate(
                        $subscription,
                        $subscriber->getSubscriptions()
                    ));
                }
            }
        }

        return $this->render('subscriber/index.html.twig', [
            'display' => $display,
            'licences' => $licences,
            'subscribers' => $subscribers,
            'seasons' => $seasons,
            'categories' => $categories
        ]);
    }
}
