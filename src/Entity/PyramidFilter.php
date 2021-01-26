<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\StatusRepository;
use Symfony\Component\Validator\Constraints as Assert;

class PyramidFilter
{

    /**
     * @Assert\NotBlank()
     */
    private Season $fromSeason;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(propertyPath="fromSeason",
     *     message="La saison de fin doit être supérieure ou égale à la saison de début")
     */
    private Season $toSeason;

    private ?array $gender = [];

    private ?Category $fromCategory = null;

    private ?Category $toCategory = null;


    /**
     * @return Season
     */
    public function getFromSeason(): Season
    {
        return $this->fromSeason;
    }

    /**
     * @param Season $fromSeason
     * @return PyramidFilter
     */
    public function setFromSeason(Season $fromSeason): self
    {
        $this->fromSeason = $fromSeason;

        return $this;
    }

    /**
     * @return Season
     */
    public function getToSeason(): Season
    {
        return $this->toSeason;
    }

    /**
     * @param Season $toSeason
     * @return PyramidFilter
     */
    public function setToSeason(Season $toSeason): self
    {
        $this->toSeason = $toSeason;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getGender(): ?array
    {
        return $this->gender;
    }

    /**
     * @param array|null $gender
     */
    public function setGender(?array $gender): self
    {
        $this->gender = $gender;

        return $this;
    }


    /**
     * @return Category|null
     */
    public function getFromCategory(): ?Category
    {
        return $this->fromCategory;
    }

    /**
     * @param Category|null $fromCategory
     * @return PyramidFilter
     */
    public function setFromCategory(?Category $fromCategory): self
    {
        $this->fromCategory = $fromCategory;

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getToCategory(): ?Category
    {
        return $this->toCategory;
    }

    /**
     * @param Category|null $toCategory
     * @return PyramidFilter
     */
    public function setToCategory(?Category $toCategory): self
    {
        $this->toCategory = $toCategory;

        return $this;
    }
}
