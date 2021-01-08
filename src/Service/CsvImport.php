<?php

namespace App\Service;

use App\Entity\Season;
use App\Entity\Subscriber;
use App\Repository\SeasonRepository;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use DateTime;

class CsvImport
{
    private DecoderInterface $csvEncoder;

    private SeasonRepository $seasonRepository;

    private SubscriberRepository $subscriberRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        DecoderInterface $csvEncoder,
        EntityManagerInterface $entityManager,
        SeasonRepository $seasonRepository,
        SubscriberRepository $subscriberRepository
    ) {
        $this->csvEncoder = $csvEncoder;
        $this->entityManager = $entityManager;
        $this->seasonRepository = $seasonRepository;
        $this->subscriberRepository = $subscriberRepository;
    }

    /**
     * @param File $import
     * @return array
     */
    public function getDataFromCsv(File $import): array
    {
        $csvString = iconv(
            'ISO-8859-1',
            'UTF-8//TRANSLIT//IGNORE',
            (string)file_get_contents($import)
        );

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

    public function createSubscribers(array $csvData): int
    {
        $total = 0;

        foreach ($csvData as $subscription) {
            // Recherche du subscriber par numéro de licence
            $subscriber = $this->subscriberRepository->findOneBy(['licenceNumber' => $subscription['NO ADHERENT']]);

            if ($subscriber == null) {
                $subscriberBirthdate = explode('/', $subscription['DATE NAISSANCE']);

                // Si le numéro de licence n'existe pas, recherche par nom/prénom/date de naissance (cas licence D)

                $subscriber = $this->subscriberRepository->findOneBy([
                    'firstname' => $subscription['PRENOM'],
                    'lastname' => $subscription['NOM'],
                    'birthdate' => (new DateTime())
                        ->setDate(
                            (int)$subscriberBirthdate[2],
                            (int)$subscriberBirthdate[1],
                            (int)$subscriberBirthdate[0]
                        )
                        ->setTime(0, 0, 0)
                ]);

                // Si on ne le trouve toujours pas, on crée un nouveau subscriber
                // Si on l'a trouvé on modifie le numéro de licence si plus récent pour le subscriber récupéré
                if ($subscriber == null) {
                    $subscriber = new Subscriber();
                    $this->entityManager->persist($subscriber);
                    $subscriber
                        ->setLicenceNumber($subscription['NO ADHERENT'])
                        ->setFirstname($subscription['PRENOM'])
                        ->setLastname($subscription['NOM'])
                        ->setGender($subscription['SEXE'])
                        ->setBirthdate((new DateTime())
                            ->setDate(
                                (int)$subscriberBirthdate[2],
                                (int)$subscriberBirthdate[1],
                                (int)$subscriberBirthdate[0]
                            )
                            ->setTime(0, 0, 0));
                    $total++;
                } elseif ($subscriber->getLicenceNumber() < $subscription['NO ADHERENT']) {
                    $subscriber->setLicenceNumber($subscription['NO ADHERENT']);
                }
            }
        }
        $this->entityManager->flush();

        return $total;
    }
}
