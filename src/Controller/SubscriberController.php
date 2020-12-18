<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Form\FilterType;
use App\Repository\SeasonRepository;
use App\Repository\SubscriberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/subscribers", name="subscribers_")
 */
class SubscriberController extends AbstractController
{
    /**
     * @Route("/{display}/filter", name="filter")
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
                'seasons' => $seasons
            ]);
        }

        return $this->render('subscriber/filter.html.twig', ['form' => $form->createView()]);
    }
}
