<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture
{
    private const SEASONS = [
        '2020-2021' => [
            'startingDate' => '2020-09-01',
            'endingDate' => '2021-06-30',
        ],
        '2019-2020' => [
            'startingDate' => '2019-09-01',
            'endingDate' => '2020-06-30',
        ],
        '2018-2019' => [
            'startingDate' => '2018-09-01',
            'endingDate' => '2020-06-30',
        ],
        '2017-2018' => [
            'startingDate' => '2017-09-01',
            'endingDate' => '2018-06-30',
        ],
        '2016-2017' => [
            'startingDate' => '2016-09-01',
            'endingDate' => '2017-06-30',
        ],
        '2015-2016' => [
            'startingDate' => '2015-09-01',
            'endingDate' => '2016-06-30',
        ],
        '2014-2015' => [
            'startingDate' => '2014-09-01',
            'endingDate' => '2015-06-30',
        ],
    ];
    public function load(ObjectManager $manager)
    {
        $index = 0;
        foreach (self::SEASONS as $name => $data) {
            $season = new Season();
            $season->setName($name);
            $season->setStartingDate(\DateTime::createFromFormat('Y-m-d', $data['startingDate']));
            $season->setEndingDate(\DateTime::createFromFormat('Y-m-d', $data['endingDate']));
            $manager->persist($season);
            $this->addReference('season_' . $index, $season);
            $index++;
        }

        $manager->flush();
    }
}
