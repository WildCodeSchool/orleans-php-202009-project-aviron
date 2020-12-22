<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    private const CATEGORIES = [
        'J9' => 'Jeune 9 ans',
        'J10' => 'Jeune 10 ans',
        'J11' => 'Jeune 11 ans',
        'J12' => 'Jeune 12 ans',
        'J13' => 'Jeune 13 ans',
        'J14' => 'Jeune 14 ans',
        'J15' => 'Jeune 15 ans',
        'J16' => 'Jeune 16 ans',
        'J17' => 'Jeune 17 ans',
        'J18' => 'Jeune 18 ans',
        'S-23' => 'Senior, moins de 23 ans',
        'S' => 'Senior, 23 et +',
    ];

    public function load(ObjectManager $manager)
    {
        $index = 0;
        foreach (self::CATEGORIES as $label => $name) {
            $category = new Category();
            $category->setName($name);
            $category->setLabel($label);
            $manager->persist($category);
            $this->addReference('category_' . $index, $category);
            $index++;
        }
        $manager->flush();
    }
}
