<?php

namespace App\DataFixtures;

use App\Service\CsvImport;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class SeasonFixtures extends Fixture implements ContainerAwareInterface, DependentFixtureInterface
{
    private const SEASONS = [
        '2010-2011' => '/src/DataFixtures/Data/FIXTURES_saison_2010-2011.csv',
        '2011-2012' => '/src/DataFixtures/Data/FIXTURES_saison_2011-2012.csv',
        '2012-2013' => '/src/DataFixtures/Data/FIXTURES_saison_2012-2013.csv',
        '2013-2014' => '/src/DataFixtures/Data/FIXTURES_saison_2013-2014.csv',
    ];


    private ContainerInterface $container;

    private CsvImport $csvImport;

    public function __construct(CsvImport $csvImport)
    {
        $this->csvImport = $csvImport;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $serializer = $this->container->get('serializer');

        foreach (self::SEASONS as $seasonName => $data) {
            $newImport = realpath("./") . $data;

            $csvString = iconv(
                'ISO-8859-1',
                'UTF-8//TRANSLIT//IGNORE',
                (string)file_get_contents($newImport)
            );

            $csvData = $serializer->decode((string)$csvString, 'csv');
            $season = $this->csvImport->createSeason($seasonName);

            $this->csvImport->createSubscriptions($csvData, $season);
        }
    }

    public function getDependencies()
    {
        return [CategoryFixtures::class, LicenceFixtures::class, StatusFixtures::class];
    }
}
