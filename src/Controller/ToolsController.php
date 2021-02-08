<?php

namespace App\Controller;

use App\Entity\Import;
use App\Form\ImportType;
use App\Repository\SeasonRepository;
use App\Service\CsvImport;
use App\Service\StatusCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/outils", name="tools_")
 */
class ToolsController extends AbstractController
{
    /**
     * @Route("/importer-une-saison", name="import", methods={"GET", "POST"})
     * @param Request $request
     * @param CsvImport $csvImport
     * @param SeasonRepository $seasonRepository
     * @param StatusCalculator $statusCalculator
     * @return Response
     */
    public function importSeason(
        Request $request,
        CsvImport $csvImport,
        SeasonRepository $seasonRepository,
        StatusCalculator $statusCalculator
    ): Response {
        $seasonImport = new Import();
        $form = $this->createForm(ImportType::class, $seasonImport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newImport = $form->getData();

            $csvData = $csvImport->getDataFromCsv($newImport->getFile());
            $season = $csvImport->createSeason($newImport->getSeasonName());

            $csvDataClean = $csvImport->csvDataManagement($csvData);

            $subscriptionCounts = $csvImport->createSubscriptions($csvDataClean, $season);
            $this->addFlash(
                'success',
                $subscriptionCounts['newSubscriptionsCount'] . ' inscription(s) ajoutée(s) en base de données'
            );

            if ($subscriptionCounts['SubscriptionDateAnomalies'] > 0) {
                $this->addFlash(
                    'danger',
                    $subscriptionCounts['SubscriptionDateAnomalies'] . ' incohérences détectées 
                entre la date de saisie dans le fichier et la saison ajoutée'
                );
            }

            $seasons = $seasonRepository->findBy([], ['name' => 'ASC']);
            $statusCalculator->calculate($seasons);

            return $this->redirectToRoute('tools_import');
        }

        return $this->render('tools/import.html.twig', [
        'form' => $form->createView(),
        ]);
    }
}
