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
     * @var SubscriptionRepository
     */
    private SubscriptionRepository $repository;

    public function __construct(SubscriptionRepository $repository)
    {
        $this->repository = $repository;
    }

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
        $actualSubscribers = $this->repository->findAllSubscribersForActualSeason(
            self::COMPETITION_LICENCE,
            $actualSeason
        );
        $youngSubscribers = $subscription->findAllYoungSubscribersForActualSeason(
            self::COMPETITION_LICENCE,
            $actualSeason,
            self::JUNIOR_CATEGORY
        );
        return $this->render(
            'home/index.html.twig',
            ['youngSubscribers' => $youngSubscribers, 'actualSubscribers' => $actualSubscribers]
        );
    }

    private function getActualSeason(SeasonRepository $season): ?string
    {
        return $season->findOneBy([], ['name' => 'DESC'])->getName();
    }
}
