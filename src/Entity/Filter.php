<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\StatusRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\ChronologicalOrder as SeasonOrder;
use App\Validator\AgeCategoryOrder as CategoryOrder;

/**
 * @SeasonOrder/ChronologicalOrder
 * @CategoryOrder/AgeCategoryOrder
 */
class Filter
{

    /**
     * @Assert\NotBlank(message="La saison de début ne peut pas être vide")
     */
    private Season $fromSeason;

    /**
     * @Assert\NotBlank(message="La saison de fin ne peut pas être vide")
     */
    private Season $toSeason;

    /**
     * @Assert\PositiveOrZero(message="Le numéro d'adhérent de début doit être supérieur ou égal à 0")
     */
    private ?int $fromAdherent = null;

    /**
     * @Assert\GreaterThanOrEqual(propertyPath="fromAdherent",
     *     message="Le numéro d'adhérent de fin doit être supérieur ou égal au numéro de début")
     * @Assert\Positive(message="Le numéro d'adhérent de fin doit être supérieur à 0")
     */
    private ?int $toAdherent = null;

    private ?array $gender = [];

    private ?array $status = [];

    /**
     * @Assert\NotBlank(message="La saison associée au statut ne peut pas être vide")
     */
    private Season $seasonStatus;

    private ?array $licences = [];

    /**
     * @Assert\NotBlank(message="La saison associée au type de licence ne peut pas être vide")
     */
    private Season $seasonLicence;

    private ?Category $fromCategory = null;

    private ?Category $toCategory = null;

    /**
     * @Assert\NotBlank(message="La saison associée à la catégorie ne peut pas être vide")
     */
    private Season $seasonCategory;

    private ?Licence $firstLicence = null;

    private ?Category $firstCategory = null;

    private bool $stillRegistered = false;

    /**
     * @Assert\Positive(message="La durée d'inscription doit être supérieure à 0")
     */
    private ?int $duration = null;

    private bool $orMore = false;

    private bool $stillAdherent = false;

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
        if (!is_null($filter->getFirstLicence())) {
            $this->setFirstLicence($licenceRepository->find($filter->getFirstLicence()->getId()));
        }
        if (!is_null($filter->getFirstCategory())) {
            $this->setFirstCategory($categoryRepository->find($filter->getFirstCategory()->getId()));
        }
        $this->setStillRegistered($filter->isStillRegistered());
        $this->setDuration($filter->getDuration());
        $this->setOrMore($filter->isOrMore());
        $this->setStillAdherent($filter->isStillAdherent());
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

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int|null $duration
     * @return Filter
     */
    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return bool
     */
    public function isOrMore(): bool
    {
        return $this->orMore;
    }

    /**
     * @param bool $orMore
     * @return Filter
     */
    public function setOrMore(bool $orMore): self
    {
        $this->orMore = $orMore;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStillAdherent(): bool
    {
        return $this->stillAdherent;
    }

    /**
     * @param bool $stillAdherent
     * @return Filter
     */
    public function setStillAdherent(bool $stillAdherent): self
    {
        $this->stillAdherent = $stillAdherent;

        return $this;
    }
}
