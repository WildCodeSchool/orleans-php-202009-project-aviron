<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="date")
     */
    private \DateTimeInterface $subscriptionDate;

    /**
     * @ORM\ManyToOne(targetEntity=Subscriber::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private Subscriber $subscriber;

    /**
     * @ORM\ManyToOne(targetEntity=Season::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private Season $season;

    /**
     * @ORM\ManyToOne(targetEntity=Licence::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private Licence $licence;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Category $category;

    /**
     * @ORM\ManyToOne(targetEntity=Status::class, inversedBy="subscriptions")
     */
    private ?Status $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscriptionDate(): ?\DateTimeInterface
    {
        return $this->subscriptionDate;
    }

    public function setSubscriptionDate(\DateTimeInterface $subscriptionDate): self
    {
        $this->subscriptionDate = $subscriptionDate;

        return $this;
    }

    public function getSubscriber(): Subscriber
    {
        return $this->subscriber;
    }

    public function setSubscriber(Subscriber $subscriber): self
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    public function getSeason(): Season
    {
        return $this->season;
    }

    public function setSeason(Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getLicence(): Licence
    {
        return $this->licence;
    }

    public function setLicence(Licence $licence): self
    {
        $this->licence = $licence;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }
}
