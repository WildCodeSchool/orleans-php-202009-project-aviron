<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Entity\Subscriber;
use App\Form\FilterType;
use App\Repository\SeasonRepository;
use App\Repository\SubscriberRepository;
use App\Service\Export;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/subscribers", name="subscribers_")
 */
class SubscriberController extends AbstractController
{
    /**
     * @Route("/{display}/filter/", name="filter")
     * @param string $display
     * @param Request $request
     * @param SubscriberRepository $subscriberRepository
     * @param SeasonRepository $seasonRepository
     * @return Response
     */
    public function filter(
        string $display,
        Request $request,
        SubscriberRepository $subscriberRepository,
        SeasonRepository $seasonRepository
    ): Response {
        $filter = new Filter();
        $form = $this->createForm(FilterType::class, $filter, ['method' => 'GET']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
            $seasons = $seasonRepository->findByFilter($filters);
            $subscribers = $subscriberRepository->findByFilter($filters);

            return $this->render('subscriber/index.html.twig', [
                'display' => $display,
                'subscribers' => $subscribers,
                'seasons' => $seasons,
                'filters' => $filters
            ]);
        }

        return $this->render('subscriber/filter.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/export/{display}", name="export")
     * @param string $display
     * @param SubscriberRepository $subscriberRepository
     * @param SeasonRepository $seasonRepository
     * @return Response
     */
    public function export(
        string $display,
        SubscriberRepository $subscriberRepository,
        SeasonRepository $seasonRepository
    ) {
        $subscribers = $subscriberRepository->findAll();
        $seasons = $seasonRepository->findAll();
        $response = new Response($this->renderView('subscriber/export.csv.twig', [
            'subscribers' => $subscribers,
            'seasons' => $seasons,
            'display' => $display,
        ]));
        $response->headers->set('Content-type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');
        return $response;
    }
}
