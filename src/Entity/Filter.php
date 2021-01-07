<?php

namespace App\Entity;

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
     *     message="La saison de fin doit être supérieure ou égale à la saison de début")
     */
    private Season $toSeason;

    private ?int $fromAdherent = null;

    /**
     * @Assert\GreaterThanOrEqual(propertyPath="fromAdherent",
     *     message="Le numéro d'adhérent de fin doit être supérieur ou égal au numéro de début")
     */
    private ?int $toAdherent = null;

    private ?string $gender = null;

    private ?array $status = [];

    /**
     * @Assert\NotBlank()
     */
    private Season $seasonStatus;

    private ?array $licences = [];

    /**
     * @Assert\NotBlank()
     */
    private Season $seasonLicence;

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

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string|null $gender
     */
    public function setGender(?string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return array|null
     */
    public function getStatus(): ?array
    {
        return $this->status;
    }

    /**
     * @param array|null $status
     */
    public function setStatus(?array $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Season
     */
    public function getSeasonStatus(): Season
    {
        return $this->seasonStatus;
    }

    /**
     * @param Season $seasonStatus
     */
    public function setSeasonStatus(Season $seasonStatus): void
    {
        $this->seasonStatus = $seasonStatus;
    }

    /**
     * @return array|null
     */
    public function getLicences(): ?array
    {
        return $this->licences;
    }

    /**
     * @param array|null $licences
     */
    public function setLicences(?array $licences): void
    {
        $this->licences = $licences;
    }

    /**
     * @return Season
     */
    public function getSeasonLicence(): Season
    {
        return $this->seasonLicence;
    }

    /**
     * @param Season $seasonLicence
     */
    public function setSeasonLicence(Season $seasonLicence): void
    {
        $this->seasonLicence = $seasonLicence;
    }
}
