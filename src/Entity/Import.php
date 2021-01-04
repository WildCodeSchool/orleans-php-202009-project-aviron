<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\File;

class Import
{
    private string $seasonName;

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
