<?php

namespace App\Entity;

use App\Repository\SeasonRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Import
{
    /**
     * @Assert\NotBlank
     * @Assert\Regex(
     *     pattern="/[0-9]{4}-[0-9]{4}/",
     *     match=true,
     *     message="Le nom de la saison n'est pas au bon format, format attendu : 2020-2021",
     * )
     */
    private string $seasonName;

    /**
     * @Assert\File(
     *     maxSize="1024000",
     *      mimeTypes = {
     *          "text/csv",
     *         "text/plain"
     *      })
     */
    private File $file;

    /**
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context): void
    {
        $seasonYears = explode('-', $this->seasonName);

        // Vérification du format des années
        if (strlen($seasonYears[0]) != 4 || strlen($seasonYears[1]) != 4) {
            $context->buildViolation('Le nom de saison attendu est "Année de début - Année de fin". 
            ' . $this->seasonName . ' n\'est pas un nom conforme.')
                ->addViolation();
        }

        // Verifie si les années se suivent dans le nom
        if ($seasonYears[1] != (int)$seasonYears[0] + 1) {
            $context->buildViolation('Les deux années doivent se suivre')
                ->addViolation();
        }
/*
        // Vérifie si le nom de saison et le fichier sont cohérents
        if (
            !str_contains(
                pathinfo($this->getFile()->getClientOriginalName(), PATHINFO_FILENAME),
                $seasonYears[0]
            )
        ) {
            $context->buildViolation('Le nom du fichier ne contient pas le nom de saison entré')
                ->addViolation();
        }

       // Vérifie que la saison entrée est bien attenante aux saisons déjà en base de données
        $firstSeason = ($this->seasonRepository->findOneBy([], ['name' => 'ASC'])->getName());
        $lastSeason = ($this->seasonRepository->findOneBy([], ['name' => 'DESC']))->getName();

        if (
            $firstSeason !== null &&
            $lastSeason !== null &&
            ($seasonYears[1] < substr($firstSeason, 0, 4) ||
                $seasonYears[0] > substr($lastSeason, 5, 4))
        ) {
            $context->buildViolation('La saison ' . $this->seasonName . ' n\'est pas attenante
            aux saisons déjà importées qui vont de ' . $firstSeason . ' à ' . $lastSeason . '.')
                ->addViolation();
        }*/
    }

    /**
     * @return string
     */
    public function getSeasonName(): string
    {
        return $this->seasonName;
    }

    /**
     * @param string $seasonName
     */
    public function setSeasonName(string $seasonName): void
    {
        $this->seasonName = $seasonName;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file): void
    {
        $this->file = $file;
    }
}
