<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Entity\PyramidFilter;
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
     * @param Request $request
     * @return Response
     */
    public function renewalPyramid(
        SeasonRepository $seasonRepository,
        LicenceRepository $licenceRepository,
        PyramidCalculator $pyramidCalculator,
        Request $request
    ): Response {

        $filter = new PyramidFilter();

        $form = $this->createForm(PyramidFilterType::class, $filter, ['method' => 'GET']);
        $form->handleRequest($request);

        $licence = $licenceRepository->findOneBy(['acronym' => self::COMPETITION_LICENCE]);

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
            $seasons = $seasonRepository->findByFilter($filters);
        } else {
            $seasons = $seasonRepository->findAll();
        }

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
