<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Status;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatusFixtures extends Fixture
{
    private const STATUS = [
        'T' => [
            'name' => 'Transfert',
            'color' => '#37cf9baa'
        ],
        'N' => [
            'name' => 'Nouveau',
            'color' => '#f2e350aa'
        ],
        'R' => [
            'name' => 'Renouvellement',
            'color' => '#e69138ff'
        ],
        'P' => [
            'name' => 'Reprise',
            'color' => '#d56741aa',
        ],
    ];

    public function load(ObjectManager $manager)
    {
        $index = 0;
        foreach (self::STATUS as $label => $data) {
            $status = new Status();
            $status->setName($data['name']);
            $status->setLabel($label);
            $status->setColor($data['color']);
            $manager->persist($status);
            $index++;
        }
        $manager->flush();
    }
}
