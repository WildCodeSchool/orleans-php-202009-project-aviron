<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Entity\Season;
use App\Form\FilterType;
use App\Repository\SeasonRepository;
use App\Repository\SubscriberRepository;
use App\Service\StatusCalculator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/subscribers", name="subscribers_")
 */
class SubscriberController extends AbstractController
{
    private const PAGINATION_LIMIT = 12;
    /**
     * @Route("/{display}/filter/", name="filter")
     * @param string $display
     * @param Request $request
     * @param SubscriberRepository $subscriberRepository
     * @param SeasonRepository $seasonRepository
     * @param StatusCalculator $statusCalculator
     * @return Response A response instance
     */
    public function filter(
        string $display,
        Request $request,
        SubscriberRepository $subscriberRepository,
        StatusCalculator $statusCalculator,
        SeasonRepository $seasonRepository,
        PaginatorInterface $paginator
    ): Response {
        $filter = new Filter();
        $filter->setSeasonStatus($seasonRepository->findOneBy([], ['id' => 'DESC']));
        $filter->setSeasonCategory($seasonRepository->findOneBy([], ['id' => 'DESC']));
        $filter->setSeasonLicence($seasonRepository->findOneBy([], ['id' => 'DESC']));
        $limitSeasons = SeasonRepository::LIMIT_NUMBER_SEASONS;
        $fromSeason = $seasonRepository->findBy([], ['id' => 'DESC'], $limitSeasons);
        $filter->setFromSeason($fromSeason[$limitSeasons - 1] ?? $seasonRepository->findOneBy([]));
        $filter->setToSeason($seasonRepository->findOneBy([], ['id' => 'DESC']));
        $form = $this->createForm(FilterType::class, $filter, ['method' => 'GET']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
            $seasons = $seasonRepository->findByFilter($filters);
            $subscribersData = $subscriberRepository->findByFilter($filters);
            $subscribers = $paginator->paginate(
                $subscribersData,
                $request->query->getint('page', 1),
                self::PAGINATION_LIMIT
            );

            $statusCalculator->calculate($seasons, $subscribersData);

            return $this->render('subscriber/index.html.twig', [
                'display' => $display,
                'subscribers' => $subscribers,
                'seasons' => $seasons,
            ]);
        }

        return $this->render('subscriber/filter.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/export/{display}", name="export")
     * @param string $display
     * @param Request $request
     * @param SubscriberRepository $subscriberRepository
     * @param SeasonRepository $seasonRepository
     * @return Response
     */
    public function export(
        string $display,
        Request $request,
        SubscriberRepository $subscriberRepository,
        SeasonRepository $seasonRepository
    ) {
        /** @var array $filtersArray */
        $filtersArray = $request->query->get('filter');
        $filters = new Filter();
        $fromSeason = $seasonRepository->find($filtersArray['fromSeason']);
        $toSeason = $seasonRepository->find($filtersArray['toSeason']);
        $filters
            ->setFromSeason($fromSeason)
            ->setToSeason($toSeason)
            ->setFromAdherent((int)$filtersArray['fromAdherent'] ?? null)
            ->setToAdherent((int)$filtersArray['toAdherent'] ?? null)
            ->setGender($filtersArray['gender'] ?? null)
            ->setStatus($filtersArray['status'][0] ?? null)
            ->setSeasonStatus($filtersArray['seasonStatus'] ?? null)
            ->setLicences($filtersArray['licences'][0] ?? null)
            ->setSeasonLicence($filtersArray['seasonLicence'] ?? null)
            ->setFromCategory($filtersArray['fromCategory'] ?? null)
            ->setToCategory($filtersArray['toCategory'] ?? null)
            ->setSeasonCategory($filtersArray['seasonCategory'] ?? null);
        $subscribers = $subscriberRepository->findByFilter($filters);
        $seasons = $seasonRepository->findByFilter($filters);
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
