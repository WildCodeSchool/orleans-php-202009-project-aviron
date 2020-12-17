<?php

namespace App\Controller;

use App\Entity\Season;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private const COMPETITION_LICENCE = 'A';

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
     * @return Response
     * @throws NonUniqueResultException
     */
    public function index(SeasonRepository $season): Response
    {
        $actualSeason = $this->getActualSeason($season);
        $actualSubscribers = $this->repository->findAllSubscribersForActualSeason(
            self::COMPETITION_LICENCE,
            $actualSeason
        );
        return $this->render('home/index.html.twig', ['actualSubscribers' => $actualSubscribers]);
    }

    private function getActualSeason(SeasonRepository $season): ?string
    {
        return $season->findOneBy([], ['name' => 'DESC'])->getName();
    }
}
