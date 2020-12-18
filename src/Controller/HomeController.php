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
     * @Route("/", name="home")
     */
    public function index(
        SeasonRepository $season,
        SubscriptionRepository $subscriptionRepo,
        LicenceRepository $licenceRepository
    ): Response {
        $licencesArray = [];
        $currentLicences = [];
        $currentSeason = $this->getCurrentSeason($season);
        $countByLicences = $subscriptionRepo->subscribersByYearByLicences($currentSeason);
        $licences = $licenceRepository->findAll();

        foreach ($licences as $object) {
            $licencesArray[] = $object->getAcronym();
        }
        for ($i = 0; $i < count($countByLicences); $i++) {
            $currentLicences[] = $countByLicences[$i]['acronym'];
        }
        foreach ($licencesArray as $licence) {
            if (!in_array($licence, $currentLicences)) {
                $countByLicences[] = ['count' => '0', 'acronym' => $licence];
            }
        }

        return $this->render('home/index.html.twig', [
            'subscribersByLicences' => $countByLicences
        ]);
    }
    private function getCurrentSeason(SeasonRepository $season): ?string
    {
        return $season->findOneBy([], ['name' => 'DESC'])->getName();
    }
}
