<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Subscriber;
use App\Repository\CategoryRepository;
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
     * @Route("/{display}", methods={"GET"}, name="index")
     * @param string $display
     * @param LicenceRepository $licenceRepository
     * @param SubscriberRepository $subscriberRepository
     * @param SeasonRepository $seasonRepository
     * @param CategoryRepository $categoryRepository
     * @return Response A response instance
     */
    public function index(
        string $display,
        LicenceRepository $licenceRepository,
        SubscriberRepository $subscriberRepository,
        SeasonRepository $seasonRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $licences = $licenceRepository->findAll();
        $subscribers = $subscriberRepository->findAll();
        $seasons = $seasonRepository->findAll();
        $categories = $categoryRepository->findAll();

        return $this->render('subscriber/index.html.twig', [
            'display' => $display,
            'licences' => $licences,
            'subscribers' => $subscribers,
            'seasons' => $seasons,
            'categories' => $categories
        ]);
    }
}
