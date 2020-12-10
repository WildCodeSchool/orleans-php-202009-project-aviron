<?php

namespace App\Controller;

use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/licences", name="licences_")
 */
class LicenceController extends AbstractController
{
    /**
     * Correspond à la route /licences/ et au name "licences_index"
     * @Route("/", name="index")
     * @param LicenceRepository $licenceRepository
     * @param SubscriberRepository $subscriberRepository
     * @param SeasonRepository $seasonRepository
     * @return Response
     */
    public function index(
        LicenceRepository $licenceRepository,
        SubscriberRepository $subscriberRepository,
        SeasonRepository $seasonRepository
    ): Response {
        $licences = $licenceRepository->findAll();
        $subscribers = $subscriberRepository->findAll();
        $seasons = $seasonRepository->findAll();

        return $this->render('licence/index.html.twig', [
            'licences' => $licences,
            'subscribers' => $subscribers,
            'seasons' => $seasons
        ]);
    }
}
