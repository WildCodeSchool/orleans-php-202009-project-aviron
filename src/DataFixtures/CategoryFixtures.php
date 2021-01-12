<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    private const CATEGORIES = [
        'J9' => [
            'name' => 'Jeune 9 ans',
            'newGroup' => 'Jeunes',
            'oldGroup' => 'Benjamin',
            'color' => '#37cf9baa'
        ],
        'J10' => [
            'name' => 'Jeune 10 ans',
            'newGroup' => 'Jeunes',
            'oldGroup' => 'Benjamin',
            'color' => '#37cf9baa'
        ],
        'J11' => [
            'name' => 'Jeune 11 ans',
            'newGroup' => 'Jeunes',
            'oldGroup' => 'Benjamin',
            'color' => '#37cf9baa'
        ],
        'J12' => [
            'name' => 'Jeune 12 ans',
            'newGroup' => 'Jeunes',
            'oldGroup' => 'Benjamin',
            'color' => '#37cf9baa'
        ],
        'J13' => [
            'name' => 'Jeune 13 ans',
            'newGroup' => 'Jeunes',
            'oldGroup' => 'Minime',
            'color' => '#f2e350aa'
        ],
        'J14' => [
            'name' => 'Jeune 14 ans',
            'newGroup' => 'Jeunes',
            'oldGroup' => 'Minime',
            'color' => '#f2e350aa'
        ],
        'J15' => [
            'name' => 'Junior 15 ans',
            'newGroup' => 'Juniors',
            'oldGroup' => 'Cadet',
            'color' => '#e69138ff'
        ],
        'J16' => [
            'name' => 'Junior 16 ans',
            'newGroup' => 'Juniors',
            'oldGroup' => 'Cadet',
            'color' => '#e69138ff'
        ],
        'J17' => [
            'name' => 'Junior 17 ans',
            'newGroup' => 'Juniors',
            'oldGroup' => 'Junior',
            'color' => '#d56741aa',
        ],
        'J18' => [
            'name' => 'Junior 18 ans',
            'newGroup' => 'Juniors',
            'oldGroup' => 'Junior',
            'color' => '#d56741aa',
        ],
        'S-23' => [
            'name' => 'Senior, moins de 23 ans',
            'newGroup' => 'Seniors',
            'oldGroup' => 'Senior B',
            'color' => '#a65bd7aa',
        ],
        'S' => [
            'name' => 'Senior, 23 et +',
            'newGroup' => 'Seniors',
            'oldGroup' => 'Senior A',
            'color' => '#6688c3aa',
        ],
    ];

    public function load(ObjectManager $manager)
    {
        $index = 0;
        foreach (self::CATEGORIES as $label => $data) {
            $category = new Category();
            $category->setName($data['name']);
            $category->setLabel($label);
            $category->setNewGroup($data['newGroup']);
            $category->setOldGroup($data['oldGroup']);
            $category->setColor($data['color']);
            $manager->persist($category);
            $this->addReference('category_' . $index, $category);
            $index++;
        }
        $manager->flush();
    }
}
