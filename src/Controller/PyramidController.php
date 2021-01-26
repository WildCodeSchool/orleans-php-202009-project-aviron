<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Form\PyramidFilterType;
use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriptionRepository;
use App\Service\PyramidCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        PyramidCalculator $pyramidCalculator,
        Request $request
    ): Response {

        $filter = new Filter();

        $form = $this->createForm(PyramidFilterType::class, $filter, ['method' => 'GET']);
        $form->handleRequest($request);

        $seasons = $seasonRepository->findAll();
        $licence = $licenceRepository->findOneBy(['acronym' => self::COMPETITION_LICENCE]);

        $renewalPyramid = $pyramidCalculator->getRenewalPyramidCounts($seasons, $licence);
        $renewalPyramidPercent = $pyramidCalculator->getRenewalPyramidPercent($renewalPyramid);
        $renewalPyramidAverage = $pyramidCalculator->getAverageRenewalPercent($renewalPyramidPercent);

        return $this->render('pyramid/pyramid.html.twig', [
            'form' => $form->createView(),
            'seasons' => $seasons,
            'renewalPyramid' => $renewalPyramid,
            'renewalPyramidPercent' => $renewalPyramidPercent,
            'renewalPercentAverage' => $renewalPyramidAverage,
        ]);
    }
}
