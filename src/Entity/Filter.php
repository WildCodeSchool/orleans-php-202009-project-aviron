<?php

namespace App\Entity;

use App\Repository\FilterRepository;
use Doctrine\ORM\Mapping as ORM;

class Filter
{

    private Season $fromSeason;

    private Season $toSeason;

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
}
