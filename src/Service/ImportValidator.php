<?php

namespace App\Service;

use App\Entity\Import;
use App\Repository\SeasonRepository;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */

class ImportValidator extends Constraint
{

    private SeasonRepository $seasonRepository;

    public function __construct(SeasonRepository $seasonRepository)
    {
        parent::__construct();
        $this->seasonRepository = $seasonRepository;
    }

    public static function validate(Import $import, ExecutionContextInterface $context): void
    {
        $seasonYears = explode('-', $import->getSeasonName());

        // Vérification du format des années
        if (strlen($seasonYears[0]) != 4 || strlen($seasonYears[1]) != 4) {
            $context->buildViolation('Le nom de saison attendu est "Année de début - Année de fin". 
            ' . $import->getSeasonName() . ' n\'est pas un nom conforme.')
                ->atPath('seasonName')
                ->addViolation();
        }

        // Verifie si les années se suivent dans le nom
        if ($seasonYears[1] != (int)$seasonYears[0] + 1) {
            $context->buildViolation('Les deux années doivent se suivre')
                ->atPath('seasonName')
                ->addViolation();
        }
/*
            // Vérifie si le nom de saison et le fichier sont cohérents
        if (
                !str_contains(
                    pathinfo($import->getFile()->getClientOriginalName(), PATHINFO_FILENAME),
                    $seasonYears[0]
                )
        ) {
            $context->buildViolation('Le nom du fichier ne contient pas le nom de saison entré')
                ->atPath('seasonName')
                ->addViolation();
        }

           // Vérifie que la saison entrée est bien attenante aux saisons déjà en base de données
            $seasonRepository = new SeasonRepository();
            $firstSeason = $seasonRepository->findOneBy([], ['name' => 'ASC'])->getName();
            $lastSeason = $seasonRepository->findOneBy([], ['name' => 'DESC'])->getName();

        if (
                $firstSeason !== null &&
                $lastSeason !== null &&
                ($seasonYears[1] < substr($firstSeason, 0, 4) ||
                    $seasonYears[0] > substr($lastSeason, 5, 4))
        ) {
            $context->buildViolation('La saison ' . $import->getSeasonName() . ' n\'est pas attenante
                aux saisons déjà importées qui vont de ' . $firstSeason . ' à ' . $lastSeason . '.')
                ->atPath('seasonName')
                ->addViolation();
        }*/
    }
}
