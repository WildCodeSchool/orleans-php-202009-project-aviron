<?php

namespace App\Service;

use App\Entity\Season;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use DateTime;

class CsvImport
{
    private DecoderInterface $csvEncoder;

    private SeasonRepository $seasonRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        DecoderInterface $csvEncoder,
        EntityManagerInterface $entityManager,
        SeasonRepository $seasonRepository
    ) {
        $this->csvEncoder = $csvEncoder;
        $this->entityManager = $entityManager;
        $this->seasonRepository = $seasonRepository;
    }

    /**
     * @param File $import
     * @return array
     */
    public function getDataFromCsv(File $import): array
    {
        $csvString = file_get_contents($import);

        return $this->csvEncoder->decode((string) $csvString, 'csv', [
            'csv_delimiter' => ';',
        ]);
    }

    /**
     * @param string $seasonName
     * @return Season
     */
    public function createSeason(string $seasonName): Season
    {
        $season = $this->seasonRepository->findOneBy(['name' => $seasonName]);

        if ($season == null) {
            $season = new Season();
            $seasonStartYear = (int)substr($seasonName, 0, 4);
            $seasonEndYear = (int)substr($seasonName, 5, 4);
            $season->setName($seasonName)
                ->setStartingDate(
                    (new DateTime())
                        ->setDate($seasonStartYear, 9, 01)
                        ->setTime(0, 0, 0)
                )
                ->setEndingDate(
                    (new DateTime())
                        ->setDate($seasonEndYear, 8, 31)
                        ->setTime(23, 59, 59)
                );
            $this->entityManager->persist($season);
            $this->entityManager->flush();
        }
        return $season;
    }
}
