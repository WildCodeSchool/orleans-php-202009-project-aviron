<?php

namespace App\DataFixtures;

use App\Entity\Licence;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LicenceFixtures extends Fixture
{
    private const LICENCES = [
        'A' => 'Compétition',
        'U' => 'Universitaire',
        'D7' => 'Découverte',
        'D30' => 'Découverte',
        'D90' => 'Découverte',
        'I' => 'Indoor'
    ];

    public function load(ObjectManager $manager)
    {
        $index = 0;
        foreach (self::LICENCES as $acronym => $name) {
            $licence = new Licence();
            $licence->setName($name);
            $licence->setAcronym($acronym);
            $manager->persist($licence);
            $this->addReference('licence_' . $index, $licence);
            $index++;
        }
        $manager->flush();
    }
}
