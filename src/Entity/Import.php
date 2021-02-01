<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\SeasonName as SeasonAssert;

class Import
{
    /**
     * @Assert\NotBlank
     * @Assert\Regex(
     *     pattern="/[0-9]{4}-[0-9]{4}/",
     *     match=true,
     *     message="Le nom de la saison n'est pas au bon format, format attendu : 2020-2021",
     * )
     * @SeasonAssert/SeasonName
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
