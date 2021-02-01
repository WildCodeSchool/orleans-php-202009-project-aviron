<?php

namespace App\Controller;

use App\Entity\PyramidFilter;
use App\Form\PyramidFilterType;
use App\Repository\SeasonRepository;
use App\Service\PyramidCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PyramidController extends AbstractController
{
    /**
     * @Route("/pyramide-des-renouvellements", name="pyramid")
     * @SuppressWarnings(PHPMD.LongVariable)
     * @param SeasonRepository $seasonRepository
     * @param PyramidCalculator $pyramidCalculator
     * @param Request $request
     * @return Response
     */
    public function renewalPyramid(
        SeasonRepository $seasonRepository,
        PyramidCalculator $pyramidCalculator,
        Request $request
    ): Response {

        $filter = new PyramidFilter();
        $filter->setToSeason($seasonRepository->findOneBy([], ['name' => 'DESC']));
        $form = $this->createForm(PyramidFilterType::class, $filter, ['method' => 'GET']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $filters = $form->getData();
                $seasons = $seasonRepository->findByFilter($filters);
        }

        isset($seasons) ? : $seasons = $seasonRepository->findAll();

        $renewalPyramid = $pyramidCalculator->getRenewalPyramidCounts($seasons, $filters ?? null);
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
