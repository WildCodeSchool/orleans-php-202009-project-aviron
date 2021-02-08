<?php

namespace App\Validator;

use App\Repository\SeasonRepository;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class SeasonNameValidator extends ConstraintValidator
{
    private SeasonRepository $seasonRepository;

    public function __construct(SeasonRepository $seasonRepository)
    {
        $this->seasonRepository = $seasonRepository;
    }

    public function validate($seasonName, Constraint $constraint): void
    {
        $seasonYears = explode('-', $seasonName);

        // Vérification du format des années
        if (strlen($seasonYears[0]) != 4 || strlen($seasonYears[1]) != 4) {
            $this->context->buildViolation('Le nom de saison attendu est "Année de début - Année de fin". 
            ' . $seasonName . ' n\'est pas un nom conforme.')
                ->addViolation();
        }

        // Verifie si les années se suivent dans le nom
        if ($seasonYears[1] != (int)$seasonYears[0] + 1) {
            $this->context->buildViolation('Les deux années doivent se suivre')
                ->addViolation();
        }

       // Vérifie que la saison entrée est bien attenante aux saisons déjà en base de données
        if (($this->seasonRepository->findOneBy([], ['name' => 'ASC'])) !== null) {
            $firstSeason = ($this->seasonRepository->findOneBy([], ['name' => 'ASC']))->getName();
            $lastSeason = ($this->seasonRepository->findOneBy([], ['name' => 'DESC']))->getName();

            if (
                $firstSeason !== null &&
                $lastSeason !== null &&
                ($seasonYears[1] < substr($firstSeason, 0, 4) ||
                    $seasonYears[0] > substr($lastSeason, 5, 4))
            ) {
                $this->context->buildViolation('La saison ' . $seasonName . ' n\'est pas attenante
                aux saisons déjà importées qui vont de ' . $firstSeason . ' à ' . $lastSeason . '.')
                    ->addViolation();
            }
        }
    }
}
