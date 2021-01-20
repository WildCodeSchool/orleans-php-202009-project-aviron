<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Form\FilterType;
use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriberRepository;
use App\Service\FirstSubscription;
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
     * @param StatusCalculator $statusCalculator
     * @param SeasonRepository $seasonRepository
     * @param PaginatorInterface $paginator
     * @param FirstSubscription $firstSubscription
     * @return Response A response instance
     */
    public function filter(
        string $display,
        Request $request,
        SubscriberRepository $subscriberRepository,
        StatusCalculator $statusCalculator,
        SeasonRepository $seasonRepository,
        PaginatorInterface $paginator,
        FirstSubscription $firstSubscription
    ): Response {
        $filter = new Filter();
        $limitSeasons = SeasonRepository::LIMIT_NUMBER_SEASONS;
        $fromSeason = $seasonRepository->findBy([], ['name' => 'DESC'], $limitSeasons);
        $filter->setFromSeason($fromSeason[$limitSeasons - 1] ?? $seasonRepository->findOneBy([]));
        $form = $this->createForm(FilterType::class, $filter, ['method' => 'GET']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
            $seasons = $seasonRepository->findByFilter($filters);
            $subscribersData = $subscriberRepository->findByFilter($filters);
            if (!empty($filters->getFirstLicence())) {
                $subscribersData = $firstSubscription->filterWith(
                    $subscribersData,
                    $filters->getFirstLicence(),
                    $filters->isStillRegistered()
                );
            }
            if (!empty($filters->getFirstCategory())) {
                $subscribersData = $firstSubscription->filterWith(
                    $subscribersData,
                    $filters->getFirstCategory(),
                    $filters->isStillRegistered()
                );
            }
            $subscribers = $paginator->paginate(
                $subscribersData,
                $request->query->getint('page', 1),
                self::PAGINATION_LIMIT
            );

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
     * @param Request $request
     * @param SubscriberRepository $subscriberRepository
     * @param SeasonRepository $seasonRepository
     * @param CategoryRepository $categoryRepository
     * @param LicenceRepository $licenceRepository
     * @return Response
     */
    public function export(
        string $display,
        Request $request,
        SubscriberRepository $subscriberRepository,
        SeasonRepository $seasonRepository,
        CategoryRepository $categoryRepository,
        LicenceRepository $licenceRepository
    ) {
        /** @var array $filtersArray */
        $filtersArray = $request->query->get('filter');
        $filters = new Filter();
        $fromSeason = $seasonRepository->find($filtersArray['fromSeason']);
        $toSeason = $seasonRepository->find($filtersArray['toSeason']);
        $seasonStatus = $seasonRepository->find($filtersArray['seasonStatus']);
        $seasonLicence = $seasonRepository->find($filtersArray['seasonLicence']);
        $fromCategory = $categoryRepository->find($filtersArray['fromCategory']);
        $toCategory = $categoryRepository->find($filtersArray['toCategory']);
        $seasonCategory = $seasonRepository->find($filtersArray['seasonCategory']);
        $firstCategory = $categoryRepository->find($filtersArray['firstCategory']);
        $firstLicence = $licenceRepository->find($filtersArray['firstLicence']);

        $filters
            ->setFromSeason($fromSeason)
            ->setToSeason($toSeason)
            ->setFromAdherent((int)$filtersArray['fromAdherent'] ?? null)
            ->setToAdherent((int)$filtersArray['toAdherent'] ?? null)
            ->setGender($filtersArray['gender'] ?? null)
            ->setStatus($filtersArray['status'][0] ?? null)
            ->setSeasonStatus($seasonStatus ?? null)
            ->setLicences($filtersArray['licences'][0] ?? null)
            ->setSeasonLicence($seasonLicence ?? null)
            ->setFromCategory($fromCategory ?? null)
            ->setToCategory($toCategory ?? null)
            ->setSeasonCategory($seasonCategory ?? null)
            ->setFirstCategory($firstCategory ?? null)
            ->setFirstLicence($firstLicence ?? null)
            ->setStillRegistered($filtersArray['stillRegistered'] ?? false);
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
