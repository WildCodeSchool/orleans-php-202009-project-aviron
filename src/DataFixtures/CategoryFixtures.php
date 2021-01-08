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
            'oldGroup' => 'Benjamin'
        ],
        'J10' => [
            'name' => 'Jeune 10 ans',
            'newGroup' => 'Jeunes',
            'oldGroup' => 'Benjamin'
        ],
        'J11' => [
            'name' => 'Jeune 11 ans',
            'newGroup' => 'Jeunes',
            'oldGroup' => 'Benjamin'
        ],
        'J12' => [
            'name' => 'Jeune 12 ans',
            'newGroup' => 'Jeunes',
            'oldGroup' => 'Benjamin'
        ],
        'J13' => [
            'name' => 'Jeune 13 ans',
            'newGroup' => 'Jeunes',
            'oldGroup' => 'Minime'
        ],
        'J14' => [
            'name' => 'Jeune 14 ans',
            'newGroup' => 'Jeunes',
            'oldGroup' => 'Minime'
        ],
        'J15' => [
            'name' => 'Junior 15 ans',
            'newGroup' => 'Juniors',
            'oldGroup' => 'Cadet'
        ],
        'J16' => [
            'name' => 'Junior 16 ans',
            'newGroup' => 'Juniors',
            'oldGroup' => 'Cadet'
        ],
        'J17' => [
            'name' => 'Junior 17 ans',
            'newGroup' => 'Juniors',
            'oldGroup' => 'Junior'
        ],
        'J18' => [
            'name' => 'Junior 18 ans',
            'newGroup' => 'Juniors',
            'oldGroup' => 'Junior'
        ],
        'S-23' => [
            'name' => 'Senior, moins de 23 ans',
            'newGroup' => 'Seniors',
            'oldGroup' => 'Senior B'
        ],
        'S' => [
            'name' => 'Senior, 23 et +',
            'newGroup' => 'Seniors',
            'oldGroup' => 'Senior A'
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
            $manager->persist($category);
            $this->addReference('category_' . $index, $category);
            $index++;
        }
        $manager->flush();
    }
}
