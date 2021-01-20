<?php

namespace App\Entity;

use App\Repository\SubscriberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriberRepository::class)
 */
class Subscriber
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
    private string $firstname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private string $lastname;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private \DateTimeInterface $birthdate;

    /**
     * @ORM\Column(type="integer")
     */
    private int $licenceNumber;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private string $gender;

    public const GENDER = ['H' => 'Homme', 'F' => 'Femme'];

    /**
     * @ORM\OneToMany(targetEntity=Subscription::class, mappedBy="subscriber")
     */
    private Collection $subscriptions;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getLicenceNumber(): int
    {
        return $this->licenceNumber;
    }

    public function setLicenceNumber(int $licenceNumber): self
    {
        $this->licenceNumber = $licenceNumber;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection|Subscription[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscriptions): self
    {
        if (!$this->subscriptions->contains($subscriptions)) {
            $this->subscriptions[] = $subscriptions;
            $subscriptions->setSubscriber($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscriptions): self
    {
        $this->subscriptions->removeElement($subscriptions);
        return $this;
    }
}
