<?php

namespace App\Entity;

use App\Repository\LicenceRepository;
use App\Service\LabelInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LicenceRepository::class)
 */
class Licence implements LabelInterface
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
     * @ORM\Column(type="string", length=10)
     */
    private string $acronym;

    /**
     * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="licence")
     */
    private Collection $subscriptions;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex("^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3}|[a-fA-F0-9]{8})$")
     */
    private ?string $color;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
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

    public function getAcronym(): ?string
    {
        return $this->acronym;
    }

    public function getLabel(): ?string
    {
        return $this->acronym;
    }

    public function setAcronym(string $acronym): self
    {
        $this->acronym = $acronym;

        return $this;
    }

    /**
     * @return Collection|Subscription[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
            $subscription->setLicence($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        $this->subscriptions->removeElement($subscription);
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }
}
