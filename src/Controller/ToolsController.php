<?php

namespace App\Controller;

use App\Entity\Import;
use App\Form\ImportType;
use App\Service\CsvImport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tools", name="tools_")
 */
class ToolsController extends AbstractController
{
    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @Route("/import", name="import", methods={"GET", "POST"})
     * @param Request $request
     * @param CsvImport $csvImport
     * @return Response
     */
    public function newSeason(
        Request $request,
        CsvImport $csvImport
    ): Response {
        $seasonImport = new Import();
        $form = $this->createForm(ImportType::class, $seasonImport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Le fichier est en cours de traitement');
            $newImport = $form->getData();
            $csvData = $csvImport->getDataFromCsv($newImport->getFile());
            $season = $csvImport->createSeason($newImport->getSeasonName());

            return $this->redirectToRoute('tools_import');
        }

        return $this->render('tools/import.html.twig', [
        'form' => $form->createView(),
        ]);
    }
}
