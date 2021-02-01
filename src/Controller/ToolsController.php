<?php

namespace App\Controller;

use App\Entity\Import;
use App\Form\ImportType;
use App\Repository\SeasonRepository;
use App\Repository\SubscriberRepository;
use App\Service\CsvImport;
use App\Service\ImportValidator;
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

            $subscriberTotal = $csvImport->createSubscriptions($csvData, $season);
            $this->addFlash('success', $subscriberTotal . ' abonné(s) importé(s) en base de données');

            $seasons = $seasonRepository->findBy([], ['name' => 'ASC']);
            $statusCalculator->calculate($seasons);

            return $this->redirectToRoute('tools_import');
        }

        return $this->render('tools/import.html.twig', [
        'form' => $form->createView(),
        ]);
    }
}
