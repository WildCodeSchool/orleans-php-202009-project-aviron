<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\StatusRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
}
