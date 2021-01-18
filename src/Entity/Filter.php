<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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

    private ?Category $fromCategory = null;

    private ?Category $toCategory = null;

    /**
     * @Assert\NotBlank()
     */
    private Season $seasonCategory;

    private ?Licence $firstLicence = null;

    private ?Category $firstCategory = null;

    private bool $stillRegistered = false;

    /**
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context): void
    {
        if (!is_null($this->getFirstLicence()) && !is_null($this->getFirstCategory())) {
            $context->buildViolation('La première inscription ne peut avoir qu\'une seule valeur')
                ->addViolation();
        }
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
     * @return Filter
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
     * @return Filter
     */
    public function setToSeason(Season $toSeason): self
    {
        $this->toSeason = $toSeason;

        return $this;
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
     * @return Filter
     */
    public function setFromAdherent(?int $fromAdherent): self
    {
        $this->fromAdherent = $fromAdherent;

        return $this;
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
     * @return Filter
     */
    public function setToAdherent(?int $toAdherent): self
    {
        $this->toAdherent = $toAdherent;

        return $this;
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
     * @return Filter
     */
    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
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
     * @return Filter
     */
    public function setStatus(?array $status): self
    {
        $this->status = $status;

        return $this;
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
     * @return Filter
     */
    public function setSeasonStatus(Season $seasonStatus): self
    {
        $this->seasonStatus = $seasonStatus;

        return $this;
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
     * @return Filter
     */
    public function setLicences(?array $licences): self
    {
        $this->licences = $licences;

        return $this;
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
     * @return Filter
     */
    public function setSeasonLicence(Season $seasonLicence): self
    {
        $this->seasonLicence = $seasonLicence;

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
     * @return Filter
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
     * @return Filter
     */
    public function setToCategory(?Category $toCategory): self
    {
        $this->toCategory = $toCategory;

        return $this;
    }

    /**
     * @return Season
     */
    public function getSeasonCategory(): Season
    {
        return $this->seasonCategory;
    }

    /**
     * @param Season $seasonCategory
     * @return Filter
     */
    public function setSeasonCategory(Season $seasonCategory): self
    {
        $this->seasonCategory = $seasonCategory;

        return $this;
    }

    /**
     * @return Licence|null
     */
    public function getFirstLicence(): ?Licence
    {
        return $this->firstLicence;
    }

    /**
     * @param Licence|null $firstLicence
     * @return Filter
     */
    public function setFirstLicence(?Licence $firstLicence): self
    {
        $this->firstLicence = $firstLicence;

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getFirstCategory(): ?Category
    {
        return $this->firstCategory;
    }

    /**
     * @param Category|null $firstCategory
     * @return Filter
     */
    public function setFirstCategory(?Category $firstCategory): self
    {
        $this->firstCategory = $firstCategory;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStillRegistered(): bool
    {
        return $this->stillRegistered;
    }

    /**
     * @param bool $stillRegistered
     * @return Filter
     */
    public function setStillRegistered(bool $stillRegistered): self
    {
        $this->stillRegistered = $stillRegistered;

        return $this;
    }
}
