<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Entity\User;
use App\Form\FilterType;
use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\StatusRepository;
use App\Repository\SubscriberRepository;
use App\Service\Registration;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FirstSubscription;
use App\Service\SubscriptionDuration;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/adherents", name="subscribers_")
 */
class SubscriberController extends AbstractController
{
    private const PAGINATION_LIMIT = 50;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @Route("/{display}/filtres/", name="filter")
     * @param string $display
     * @param Request $request
     * @param SubscriberRepository $subscriberRepository
     * @param SeasonRepository $seasonRepository
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $entityManager
     * @param StatusRepository $statusRepository
     * @param LicenceRepository $licenceRepository
     * @param CategoryRepository $categoryRepository
     * @param FirstSubscription $firstSubscription
     * @param SubscriptionDuration $subscriptionDuration
     * @param Registration $registration
     * @return Response A response instance
     */
    public function filter(
        string $display,
        Request $request,
        SubscriberRepository $subscriberRepository,
        SeasonRepository $seasonRepository,
        PaginatorInterface $paginator,
        EntityManagerInterface $entityManager,
        StatusRepository $statusRepository,
        LicenceRepository $licenceRepository,
        CategoryRepository $categoryRepository,
        FirstSubscription $firstSubscription,
        SubscriptionDuration $subscriptionDuration,
        Registration $registration
    ): Response {
        $filter = new Filter();
        $user = $this->getUser();
        if (!is_null($user) && $user instanceof User && !is_null($user->getLastSearch())) {
            $lastSearch = unserialize($user->getLastSearch()[0], ['allowed_classes' => true]);
            $filter->hydrate(
                $lastSearch,
                $seasonRepository,
                $statusRepository,
                $licenceRepository,
                $categoryRepository
            );
        } else {
            $limitSeasons = SeasonRepository::LIMIT_NUMBER_SEASONS;
            $fromSeason = $seasonRepository->findBy([], ['id' => 'DESC'], $limitSeasons);
            $filter->setFromSeason($fromSeason[$limitSeasons - 1] ?? $seasonRepository->findOneBy([]));
            $filter->setToSeason($seasonRepository->findOneBy([], ['name' => 'DESC']));
        }
        $form = $this->createForm(FilterType::class, $filter, ['method' => 'GET']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
            $seasons = $seasonRepository->findByFilter($filters);
            $subscribersData = $subscriberRepository->findByFilter($filters);
            if (!empty($filter->getStatus())) {
                $subscribersData = $registration->filterWithSeasonAndWithStatus(
                    $subscribersData,
                    $filter->getStatus(),
                    $filter->getSeasonStatus()
                );
            }
            if (!empty($filter->getLicences())) {
                $subscribersData = $registration->filterWithSeasonAndWithLicence(
                    $subscribersData,
                    $filter->getLicences(),
                    $filter->getSeasonLicence()
                );
            }
            if (!empty($filter->getFromCategory()) || !empty($filter->getToCategory())) {
                $subscribersData = $registration->filterWithSeasonAndWithCategories(
                    $subscribersData,
                    $filter->getFromCategory(),
                    $filter->getToCategory(),
                    $filter->getSeasonCategory()
                );
            }
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
            if (!empty($filters->getDuration())) {
                $subscribersData = $subscriptionDuration->filterBy(
                    $subscribersData,
                    $filters->getDuration(),
                    $filters->isOrMore(),
                    $filters->isStillAdherent()
                );
            }
            $subscribers = $paginator->paginate(
                $subscribersData,
                $request->query->getint('page', 1),
                self::PAGINATION_LIMIT
            );

            $user instanceof User ? $user->setLastSearch((array)serialize($filters)) : false;
            $entityManager->flush();
            $numberResults = count($subscribersData);
            $categories = $categoryRepository->findAll();
            $categoriesDB = [];
            $key = '';
            foreach ($categories as $category) {
                if ($category->getColor() === $key) {
                    $categoriesDB[$key] .= ', ' . $category->getLabel();
                } else {
                    $key = $category->getColor();
                    $categoriesDB[$key] = $category->getOldGroup() . ' : ' . $category->getLabel();
                }
            }

            return $this->render('subscriber/index.html.twig', [
                'display' => $display,
                'subscribers' => $subscribers,
                'seasons' => $seasons,
                'filters' => $filters,
                'statusDB' => $statusRepository->findAll(),
                'categoriesDB' => $categoriesDB,
                'licencesDB' => $licenceRepository->findAllGroupByName(),
                'numberResults' => $numberResults
            ]);
        }

        return $this->render('subscriber/filter.html.twig', [
            'form' => $form->createView(),
            'display' => $display
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @Route("/export/{display}", name="export")
     * @param string $display
     * @param SubscriberRepository $subscriberRepository
     * @param SeasonRepository $seasonRepository
     * @param CategoryRepository $categoryRepository
     * @param LicenceRepository $licenceRepository
     * @return Response
     */
    public function export(
        string $display,
        SubscriberRepository $subscriberRepository,
        SeasonRepository $seasonRepository,
        CategoryRepository $categoryRepository,
        LicenceRepository $licenceRepository,
        FirstSubscription $firstSubscription,
        SubscriptionDuration $subscriptionDuration,
        StatusRepository $statusRepository,
        Registration $registration
    ) {
        $filters = new Filter();
        $user = $this->getUser();
        if (!is_null($user) && $user instanceof User && !is_null($user->getLastSearch())) {
            $lastSearch = unserialize($user->getLastSearch()[0], ['allowed_classes' => true]);
            $filters->hydrate(
                $lastSearch,
                $seasonRepository,
                $statusRepository,
                $licenceRepository,
                $categoryRepository
            );
        }

        $seasons = $seasonRepository->findByFilter($filters);
        $subscribers = $subscriberRepository->findByFilter($filters);

        if (!empty($filters->getFirstLicence())) {
            $subscribers = $firstSubscription->filterWith(
                $subscribers,
                $filters->getFirstLicence(),
                $filters->isStillRegistered()
            );
        }
        if (!empty($filters->getFirstCategory())) {
            $subscribers = $firstSubscription->filterWith(
                $subscribers,
                $filters->getFirstCategory(),
                $filters->isStillRegistered()
            );
        }
        if (!empty($filters->getDuration())) {
            $subscribers = $subscriptionDuration->filterBy(
                $subscribers,
                $filters->getDuration(),
                $filters->isOrMore(),
                $filters->isStillAdherent()
            );
        }

        if (!empty($filters->getStatus())) {
            $subscribers = $registration->filterWithSeasonAndWithStatus(
                $subscribers,
                $filters->getStatus(),
                $filters->getSeasonStatus()
            );
        }
        if (!empty($filters->getLicences())) {
            $subscribers = $registration->filterWithSeasonAndWithLicence(
                $subscribers,
                $filters->getLicences(),
                $filters->getSeasonLicence()
            );
        }
        if (!empty($filters->getFromCategory()) || !empty($filters->getToCategory())) {
            $subscribers = $registration->filterWithSeasonAndWithCategories(
                $subscribers,
                $filters->getFromCategory(),
                $filters->getToCategory(),
                $filters->getSeasonCategory()
            );
        }

        $response = new Response($this->renderView('subscriber/export.csv.twig', [
            'subscribers' => $subscribers,
            'seasons' => $seasons,
            'display' => $display,
        ]));
        $response->headers->set('Content-type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');
        return $response;
    }

    /**
     * @Route("/reinitialisation/{display}", name="reinitialisation")
     * @param EntityManagerInterface $entityManager
     * @param string $display
     * @return Response
     */
    public function reinitialisation(EntityManagerInterface $entityManager, string $display): Response
    {
        $user = $this->getUser();
        $user instanceof User ? $user->setLastSearch(null) : false;
        $entityManager->flush();

        return $this->redirectToRoute('subscribers_filter', ['display' => $display]);
    }
}
