<?php

namespace App\Controller;

use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

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
     * @param LicenceRepository $licence
     * @return Response
     */
    public function index(SeasonRepository $season, LicenceRepository $licence): Response
    {
        $actualSeason = $this->getActualSeason($season);
        $licenceAcronym = $this->getLicenceAcronym($licence);
        $actualSubscribers = $this->repository->findAllSubscribersForActualSeason($licenceAcronym, $actualSeason);
        return $this->render('home/index.html.twig', ['actualSubscribers' => $actualSubscribers]);
    }

    private function getActualSeason(SeasonRepository $season): ?string
    {
        $actualSeason = $season->findBy([], ['name' => 'DESC'], 1);
        return $actualSeason[0]->getName();
    }

    private function getLicenceAcronym(LicenceRepository $licence): ?string
    {
        $competitionLicence = $licence->findBy(['acronym' => 'A']);
        return $competitionLicence[0]->getAcronym();
    }
}
