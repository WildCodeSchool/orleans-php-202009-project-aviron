<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PyramidController extends AbstractController
{
    private const COMPETITION_LICENCE = 'A';

    /**
     * @Route("/pyramide-des-renouvellements", name="pyramid")
     * @SuppressWarnings(PHPMD.LongVariable)
     * @param SeasonRepository $seasonRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param LicenceRepository $licenceRepository
     * @return Response
     */
    public function renewalPyramid(
        SeasonRepository $seasonRepository,
        SubscriptionRepository $subscriptionRepository,
        LicenceRepository $licenceRepository
    ): Response {
        $seasons = $seasonRepository->findAll();
        $licence = $licenceRepository->findOneBy(['acronym' => self::COMPETITION_LICENCE]);
        $renewalPyramid = [];

        for ($i = 0; $i < count($seasons); $i++) {
            $seasonSubscriptions = $subscriptionRepository->findBy(['season' => $seasons[$i], 'licence' => $licence]);
            $renewalSeason = [];

            for ($j = 0; $j < count($seasons); $j++) {
                if ($j < $i) {
                    $renewalSeason[] = null;
                } elseif ($j === $i) {
                    $renewalSeason[] = count($seasonSubscriptions);
                } else {
                    $renewSubscriptions = 0;
                    foreach ($seasonSubscriptions as $seasonSubscription) {
                        $seasonSubscriberSubscriptions = $seasonSubscription->getSubscriber()->getSubscriptions();
                        $seasonSubscriberSeasons = [];
                        foreach ($seasonSubscriberSubscriptions as $seasonSubscriberSubscription) {
                            $seasonSubscriberSeasons[] = $seasonSubscriberSubscription->getSeason()->getName();
                        }
                        if (in_array($seasons[$j]->getName(), $seasonSubscriberSeasons)) {
                            $renewSubscriptions++;
                        }
                    }
                    $renewalSeason[] = $renewSubscriptions;
                }
            }

            $renewalPyramid[$seasons[$i]->getName()] = $renewalSeason;
        }

        return $this->render('pyramid/pyramid.html.twig', [
            'seasons' => $seasons,
            'renewalPyramid' => $renewalPyramid
        ]);
    }
}
