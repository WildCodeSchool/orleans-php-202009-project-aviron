<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Service\LabelInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category implements LabelInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private ?string $label;

    /**
     * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="category")
     */
    private Collection $subscriptions;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private string $newGroup;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private string $oldGroup;

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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

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
            $subscription->setCategory($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getCategory() === $this) {
                $subscription->setCategory(null);
            }
        }

        return $this;
    }

    public function getNewGroup(): ?string
    {
        return $this->newGroup;
    }

    public function setNewGroup(string $newGroup): self
    {
        $this->newGroup = $newGroup;

        return $this;
    }

    public function getOldGroup(): ?string
    {
        return $this->oldGroup;
    }

    public function setOldGroup(string $oldGroup): self
    {
        $this->oldGroup = $oldGroup;

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
