<?php

namespace App\Controller;

use App\Entity\Import;
use App\Form\ImportType;
use App\Service\CsvParser;
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
     * @Route("/import", name="import", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function newSeason(Request $request, CsvParser $csvParser): Response
    {
        $seasonImport = new Import();
        $form = $this->createForm(ImportType::class, $seasonImport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Le fichier est en cours de traitement');
            $newImport = $form->getData();
            $fileData = $csvParser->getDataFromCsv($newImport->getFile());
            dump($fileData);

            return $this->redirectToRoute('tools_import');
        }

        return $this->render('tools/import.html.twig', [
        'form' => $form->createView(),
        ]);
    }
}
