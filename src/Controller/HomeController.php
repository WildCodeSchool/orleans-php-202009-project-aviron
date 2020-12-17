<?php

namespace App\Controller;

use App\Repository\SeasonRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private const COMPETITION_LICENCE = 'A';
    private const JUNIOR_CATEGORY = 'J';

    /**
     * @Route("/", name="home")
     * @param SeasonRepository $season
     * @param SubscriptionRepository $subscription
     * @return Response
     * @throws NonUniqueResultException
     */
    public function index(SeasonRepository $season, SubscriptionRepository $subscription): Response
    {
        $actualSeason = $this->getActualSeason($season);
        dump($actualSeason);
        $youngSubscribers = $subscription->findAllYoungSubscribersForActualSeason(
            self::COMPETITION_LICENCE,
            $actualSeason,
            self::JUNIOR_CATEGORY
        );
        dump($actualSeason);
        dump($youngSubscribers);
        return $this->render('home/index.html.twig', ['youngSubscribers' => $youngSubscribers]);
    }

    private function getActualSeason(SeasonRepository $season): ?string
    {
        return $season->findOneBy([], ['name' => 'DESC'])->getName();
    }
}
