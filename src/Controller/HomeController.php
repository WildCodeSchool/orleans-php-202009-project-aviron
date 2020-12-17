<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Repository\SeasonRepository;
use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param SeasonRepository $seasonRepository
     * @param SubscriptionRepository $subscriptionRepo
     * @return Response
     */
    public function index(SeasonRepository $seasonRepository, SubscriptionRepository $subscriptionRepo): Response
    {
        $season = $this->getCurrentSeason($seasonRepository);
        $countByLicences = $subscriptionRepo->subscribersByYearByLicences($season);
        return $this->render('home/index.html.twig', [
            'subscribersByLicences' => $countByLicences
        ]);
    }

    private function getCurrentSeason(SeasonRepository $seasonRepository): ?string
    {
        $currentSeason = $seasonRepository->findBy([], ['name' => 'DESC']);
        return $currentSeason[0]->getName();
    }
}
