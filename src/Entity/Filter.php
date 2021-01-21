<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\StatusRepository;
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

    private ?Category $fromCategory = null;

    private ?Category $toCategory = null;

    /**
     * @Assert\NotBlank()
     */
    private Season $seasonCategory;

    public function hydrate(
        Filter $filter,
        SeasonRepository $seasonRepository,
        StatusRepository $statusRepository,
        LicenceRepository $licenceRepository,
        CategoryRepository $categoryRepository
    ): self {
        $this->setFromSeason($seasonRepository->find($filter->getFromSeason()));
        $this->setToSeason($seasonRepository->find($filter->getToSeason()));
        $this->setSeasonCategory($seasonRepository->find($filter->getSeasonCategory()->getId()));
        $this->setSeasonLicence($seasonRepository->find($filter->getSeasonLicence()->getId()));
        $this->setSeasonStatus($seasonRepository->find($filter->getSeasonStatus()->getId()));
        $this->setFromAdherent($filter->getFromAdherent());
        $this->setToAdherent($filter->getToAdherent());
        $this->setGender($filter->getGender());
        if (!is_null($filter->getStatus())) {
            $this->setStatus(array_map(function ($status) use ($statusRepository) {
                return $statusRepository->find($status->getId());
            }, $filter->getStatus()));
        }
        if (!is_null($filter->getLicences())) {
            $this->setLicences(array_map(function ($licence) use ($licenceRepository) {
                return $licenceRepository->find($licence->getId());
            }, $filter->getLicences()));
        }
        if (!is_null($filter->getFromCategory())) {
            $this->setFromCategory($categoryRepository->find($filter->getFromCategory()->getId()));
        }
        if (!is_null($filter->getToCategory())) {
            $this->setToCategory($categoryRepository->find($filter->getToCategory()->getId()));
        }
        return $this;
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
     */
    public function setSeasonCategory(Season $seasonCategory): self
    {
        $this->seasonCategory = $seasonCategory;

        return $this;
    }
}
