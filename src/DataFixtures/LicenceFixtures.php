<?php

namespace App\DataFixtures;

use App\Entity\Licence;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LicenceFixtures extends Fixture
{
    private const LICENCES = [
        'A' => [
            'name' => 'Compétition',
            'color' => '#6688c3aa'
        ],
        'U' => [
            'name' => 'Universitaire',
            'color' => '#a65bd7aa'
        ],
        'D7' => [
            'name' => 'Découverte',
            'color' => '#37cf9baa'
        ],
        'D30' => [
            'name' => 'Découverte',
            'color' => '#37cf9baa'
        ],
        'D90' => [
            'name' => 'Découverte',
            'color' => '#37cf9baa'
        ],
        'I' => [
            'name' => 'Indoor',
            'color' => '#f2e350aa'
        ],
    ];

    public function load(ObjectManager $manager)
    {
        $index = 0;
        foreach (self::LICENCES as $acronym => $data) {
            $licence = new Licence();
            $licence->setName($data['name']);
            $licence->setColor($data['color']);
            $licence->setAcronym($acronym);
            $manager->persist($licence);
            $this->addReference('licence_' . $index, $licence);
            $index++;
        }
        $manager->flush();
    }
}
