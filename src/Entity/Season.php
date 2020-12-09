<?php

namespace App\Entity;

use App\Repository\SeasonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SeasonRepository::class)
 */
class Season
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private string $name;

    /**
     * @ORM\Column(type="date")
     */
    private \DateTimeInterface $startingDate;

    /**
     * @ORM\Column(type="date")
     */
    private \DateTimeInterface $endingDate;

    /**
     * @ORM\OneToMany(targetEntity=SubscriberSeason::class, mappedBy="season")
     */
    private Collection $subscriberSeasons;

    public function __construct()
    {
        $this->subscriberSeasons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStartingDate(): ?\DateTimeInterface
    {
        return $this->startingDate;
    }

    public function setStartingDate(\DateTimeInterface $startingDate): self
    {
        $this->startingDate = $startingDate;

        return $this;
    }

    public function getEndingDate(): ?\DateTimeInterface
    {
        return $this->endingDate;
    }

    public function setEndingDate(\DateTimeInterface $endingDate): self
    {
        $this->endingDate = $endingDate;

        return $this;
    }

    /**
     * @return Collection|SubscriberSeason[]
     */
    public function getSubscriberSeasons(): Collection
    {
        return $this->subscriberSeasons;
    }

    public function addSubscriberSeason(SubscriberSeason $subscriberSeason): self
    {
        if (!$this->subscriberSeasons->contains($subscriberSeason)) {
            $this->subscriberSeasons[] = $subscriberSeason;
            $subscriberSeason->setSeason($this);
        }

        return $this;
    }

    public function removeSubscriberSeason(SubscriberSeason $subscriberSeason): self
    {
        if ($this->subscriberSeasons->removeElement($subscriberSeason)) {
            // set the owning side to null (unless already changed)
            if ($subscriberSeason->getSeason() === $this) {
                $subscriberSeason->setSeason(null);
            }
        }

        return $this;
    }
}
