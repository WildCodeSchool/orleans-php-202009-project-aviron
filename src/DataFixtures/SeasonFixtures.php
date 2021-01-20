<?php

namespace App\DataFixtures;

use App\Repository\SeasonRepository;
use App\Service\CsvImport;
use App\Service\StatusCalculator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    private const SEASONS = [
        '2010-2011' => '/src/DataFixtures/Data/FIXTURES_saison_2010-2011.csv',
        '2011-2012' => '/src/DataFixtures/Data/FIXTURES_saison_2011-2012.csv',
        '2012-2013' => '/src/DataFixtures/Data/FIXTURES_saison_2012-2013.csv',
        '2013-2014' => '/src/DataFixtures/Data/FIXTURES_saison_2013-2014.csv',
    ];

    private CsvImport $csvImport;
    /**
     * @var DecoderInterface
     */
    private DecoderInterface $csvEncoder;

    private SeasonRepository $seasonRepository;

    private StatusCalculator $statusCalculator;

    public function __construct(
        CsvImport $csvImport,
        DecoderInterface $csvEncoder,
        SeasonRepository $seasonRepository,
        StatusCalculator $statusCalculator
    ) {
        $this->csvImport = $csvImport;
        $this->csvEncoder = $csvEncoder;
        $this->seasonRepository = $seasonRepository;
        $this->statusCalculator = $statusCalculator;
    }

    public function load(ObjectManager $manager)
    {

        foreach (self::SEASONS as $seasonName => $data) {
            $newImport = realpath("./") . $data;

            $csvString = iconv(
                'ISO-8859-1',
                'UTF-8//TRANSLIT//IGNORE',
                (string)file_get_contents($newImport)
            );

            $csvData = $this->csvEncoder->decode((string) $csvString, 'csv', [
                'csv_delimiter' => ';',
            ]);

            $season = $this->csvImport->createSeason($seasonName);

            $this->csvImport->createSubscriptions($csvData, $season);
        }

        $seasons = $this->seasonRepository->findBy([], ['name' => 'ASC']);
        $this->statusCalculator->calculate($seasons);
    }

    public function getDependencies()
    {
        return [CategoryFixtures::class, LicenceFixtures::class, StatusFixtures::class];
    }
}
