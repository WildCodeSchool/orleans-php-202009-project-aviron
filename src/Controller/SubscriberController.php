<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Subscriber;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriberRepository;
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
     * @Route("/{display}", name="index")
     * @param string $display
     * @param LicenceRepository $licenceRepository
     * @param SubscriberRepository $subscriberRepository
     * @param SeasonRepository $seasonRepository
     * @return Response A response instance
     */
    public function index(
        string $display,
        LicenceRepository $licenceRepository,
        SubscriberRepository $subscriberRepository,
        SeasonRepository $seasonRepository
    ): Response {
        $licences = $licenceRepository->findAll();
        $subscribers = $subscriberRepository->findAll();
        $seasons = $seasonRepository->findAll();

        return $this->render('licence/index.html.twig', [
            'display' => $display,
            'licences' => $licences,
            'subscribers' => $subscribers,
            'seasons' => $seasons
        ]);
    }
}
