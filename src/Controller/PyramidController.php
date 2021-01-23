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

    /**
     * @Route("/pyramide-des-renouvellements", name="pyramid")
     * @SuppressWarnings(PHPMD.LongVariable)
     * @param SeasonRepository $seasonRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @return Response
     */
    public function renewalPyramid(
        SeasonRepository $seasonRepository,
        SubscriptionRepository $subscriptionRepository
    ): Response {
        return $this->render('pyramid/pyramid.html.twig', [
        ]);
    }
}
