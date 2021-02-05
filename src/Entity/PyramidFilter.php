<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\ChronologicalOrder as SeasonOrder;
use App\Validator\AgeCategoryOrder as CategoryOrder;

/**
 * @SeasonOrder/ChronologicalOrder
 * @CategoryOrder/AgeCategoryOrder
 */

class PyramidFilter
{
    /**
     * @Assert\NotBlank()
     */
    private Season $fromSeason;

    /**
     * @Assert\NotBlank()
     */
    private Season $toSeason;

    private ?array $gender = [];

    private ?Category $fromCategory = null;

    private ?Category $toCategory = null;

    private bool $newSubscriber = false;

    private bool $licenceU = false;

    /**
     * @return bool
     */
    public function isLicenceU(): bool
    {
        return $this->licenceU;
    }

    /**
     * @param bool $licenceU
     */
    public function setLicenceU(bool $licenceU): void
    {
        $this->licenceU = $licenceU;
    }


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
     * @return PyramidFilter
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

    /**
     * @return bool
     */
    public function isNewSubscriber(): bool
    {
        return $this->newSubscriber;
    }

    /**
     * @param bool $newSubscriber
     */
    public function setNewSubscriber(bool $newSubscriber): void
    {
        $this->newSubscriber = $newSubscriber;
    }
}
