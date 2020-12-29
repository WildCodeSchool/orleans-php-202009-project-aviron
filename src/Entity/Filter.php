<?php

namespace App\Entity;

use App\Repository\FilterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class Filter
{

    /**
     * @Assert\NotBlank()
     */
    private Season $fromSeason;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(propertyPath="fromSeason",
     *     message="Cette valeur doit être supérieure ou égale à la saison de départ")
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
     */
    public function setFromSeason(Season $fromSeason): void
    {
        $this->fromSeason = $fromSeason;
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
     */
    public function setToSeason(Season $toSeason): void
    {
        $this->toSeason = $toSeason;
    }
}
