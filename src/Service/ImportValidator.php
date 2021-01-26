<?php

namespace App\Service;

use App\Repository\SeasonRepository;

use function Amp\Promise\first;

class ImportValidator
{

    private SeasonRepository $seasonRepository;

    public function __construct(SeasonRepository $seasonRepository)
    {
        $this->seasonRepository = $seasonRepository;
    }

    public function validateSeasonName(string $seasonName, string $fileName): array
    {
        $errors = [];

        $seasonYears = explode('-', $seasonName);

        // Vérification du format des années
        if (strlen($seasonYears[0]) != 4 || strlen($seasonYears[1]) != 4) {
            $errors['yearLength'] =
                'Le nom de saison attendu est "Année de début - Année de fin". ' . $seasonName .
                ' n\'est pas un nom conforme.';
        }

        // Verifie si les années se suivent dans le nom
        if ($seasonYears[1] != (int)$seasonYears[0] + 1) {
            $errors['seasonYears'] =
               'Les deux années doivent se suivre';
        }

        // Vérifie si le nom de saison et le fichier sont cohérents
        if (!str_contains($fileName, $seasonYears[0])) {
            $errors['filename'] =
                'Le fichier ne semble pas correspondre à la saison entrée';
        }

        // Vérifie que la saison entrée est bien attenante aux saisons déjà en base de données
        $firstSeason = $this->seasonRepository->findOneBy([], ['name' => 'ASC'])->getName();
        $lastSeason = $this->seasonRepository->findOneBy([], ['name' => 'DESC'])->getName();

        if (
            $firstSeason !== null &&
            $lastSeason !== null &&
            ($seasonYears[1] < substr($firstSeason, 0, 4) ||
                $seasonYears[0] > substr($lastSeason, 5, 4))
        ) {
            $errors['continuity'] =
                'La saison ' . $seasonName . ' n\'est pas attenante aux saisons déjà importées qui vont de ' .
                $firstSeason . ' à ' . $lastSeason . '.';
        }

        return $errors;
    }
}
