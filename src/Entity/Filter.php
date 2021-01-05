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

    /**
     * @Assert\Choice(choices=Subscriber::GENDER,
     *     message="Le sexe choisi n'est pas une valeur valide")
     */
    private ?string $gender = null;

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
}
