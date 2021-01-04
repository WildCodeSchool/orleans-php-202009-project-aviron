<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Filter
{

    private Season $fromSeason;

    private Season $toSeason;

    private ?int $fromAdherent = null;

    /**
     * @Assert\GreaterThanOrEqual(propertyPath="fromAdherent",
     *     message="Le numéro d'adhérent de fin doit être supérieur ou égal au numéro de début")
     */
    private ?int $toAdherent = null;

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

    /**
     * @return int|null
     */
    public function getFromAdherent(): ?int
    {
        return $this->fromAdherent;
    }

    /**
     * @param int|null $fromAdherent
     */
    public function setFromAdherent(?int $fromAdherent): void
    {
        $this->fromAdherent = $fromAdherent;
    }

    /**
     * @return int|null
     */
    public function getToAdherent(): ?int
    {
        return $this->toAdherent;
    }

    /**
     * @param int|null $toAdherent
     */
    public function setToAdherent(?int $toAdherent): void
    {
        $this->toAdherent = $toAdherent;
    }
}
