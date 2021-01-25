<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriptionRepository;
use App\Service\PyramidCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PyramidController extends AbstractController
{
    private const COMPETITION_LICENCE = 'A';

    /**
     * @Route("/pyramide-des-renouvellements", name="pyramid")
     * @SuppressWarnings(PHPMD.LongVariable)
     * @param SeasonRepository $seasonRepository
     * @param LicenceRepository $licenceRepository
     * @param PyramidCalculator $pyramidCalculator
     * @return Response
     */
    public function renewalPyramid(
        SeasonRepository $seasonRepository,
        LicenceRepository $licenceRepository,
        PyramidCalculator $pyramidCalculator
    ): Response {
        $seasons = $seasonRepository->findAll();
        $licence = $licenceRepository->findOneBy(['acronym' => self::COMPETITION_LICENCE]);

        $renewalPyramid = $pyramidCalculator->getRenewalPyramidCounts($seasons, $licence);
        $renewalPyramidPercent = $pyramidCalculator->getRenewalPyramidPercent($renewalPyramid);

        return $this->render('pyramid/pyramid.html.twig', [
            'seasons' => $seasons,
            'renewalPyramid' => $renewalPyramid,
            'renewalPyramidPercent' => $renewalPyramidPercent,

        ]);
    }
}
