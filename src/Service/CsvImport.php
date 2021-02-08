<?php

namespace App\Service;

use App\Entity\Licence;
use App\Entity\Season;
use App\Entity\Subscriber;
use App\Entity\Subscription;
use App\Repository\CategoryRepository;
use App\Repository\LicenceRepository;
use App\Repository\SeasonRepository;
use App\Repository\SubscriberRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use DateTime;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CsvImport
{
    private DecoderInterface $csvEncoder;

    private SeasonRepository $seasonRepository;

    private SubscriberRepository $subscriberRepository;

    private SubscriptionRepository $subscriptionRepository;

    private EntityManagerInterface $entityManager;

    private LicenceRepository $licenceRepository;

    private CategoryRepository $categoryRepository;


    /**
     * @param DecoderInterface $csvEncoder
     * @param EntityManagerInterface $entityManager
     * @param SeasonRepository $seasonRepository
     * @param SubscriberRepository $subscriberRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param LicenceRepository $licenceRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        DecoderInterface $csvEncoder,
        EntityManagerInterface $entityManager,
        SeasonRepository $seasonRepository,
        SubscriberRepository $subscriberRepository,
        SubscriptionRepository $subscriptionRepository,
        LicenceRepository $licenceRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->csvEncoder = $csvEncoder;
        $this->entityManager = $entityManager;
        $this->seasonRepository = $seasonRepository;
        $this->subscriberRepository = $subscriberRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->licenceRepository = $licenceRepository;
        $this->categoryRepository = $categoryRepository;
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

    public function csvDataManagement(array $csvData): array
    {
        $csvClean = [];
        // Pour chaque ligne on crée un entrée dans le tableau clean avec le numéro d'adhérent en clé.
        // S'il existe déjà, la ligne se met à jour avec la dernière entrée
        foreach ($csvData as $csvLine) {
            $csvClean[$csvLine['NO ADHERENT']] = $csvLine;
        }
        return $csvClean;
    }

    /**
     * @param string $seasonName
     * @return Season
     */
    public function createSeason(string $seasonName): Season
    {
        $season = $this->seasonRepository->findOneBy(['name' => $seasonName]);

        if (!$season instanceof Season) {
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

    /**
     * @SuppressWarnings(PHPMD.LongVariable)
     * @param array $csvData
     * @param Season $season
     * @return array
     */
    public function createSubscriptions(array $csvData, Season $season): array
    {
        $counts = [
            'newSubscriptionsCount' => 0,
            'SubscriptionDateAnomalies' => 0
        ];

        foreach ($csvData as $csvLine) {
            // vérification de la date de saisie
            $subscriptionDate = $this->stringToDatetime($csvLine['DATE DE SAISIE']);

            if ($subscriptionDate < $season->getStartingDate() || $subscriptionDate > $season->getEndingDate()) {
                $counts['SubscriptionDateAnomalies'] ++;
            } else {
            // Recherche du subscriber par numéro de licence
                $subscriber = $this->subscriberRepository->findOneBy(['licenceNumber' => $csvLine['NO ADHERENT']]);

                if (!$subscriber instanceof Subscriber) {
                    // Si le numéro de licence n'existe pas, recherche par nom/prénom/date de naissance (cas licence D)

                    $subscriber = $this->subscriberRepository->findOneBy([
                    'firstname' => $csvLine['PRENOM'],
                    'lastname' => $csvLine['NOM'],
                    'birthdate' => $this->stringToDatetime($csvLine['DATE NAISSANCE'])
                    ]);

                    // Si on ne le trouve toujours pas, on crée un nouveau subscriber
                    // Si on l'a trouvé on modifie le numéro de licence si plus récent pour le subscriber récupéré
                    if (!$subscriber instanceof Subscriber) {
                        $subscriber = new Subscriber();
                        $this->entityManager->persist($subscriber);
                        $subscriber
                            ->setLicenceNumber($csvLine['NO ADHERENT'])
                            ->setFirstname($csvLine['PRENOM'])
                            ->setLastname($csvLine['NOM'])
                            ->setGender($csvLine['SEXE'])
                            ->setBirthdate($this->stringToDatetime($csvLine['DATE NAISSANCE']));
                    } elseif ($subscriber->getLicenceNumber() < $csvLine['NO ADHERENT']) {
                        $subscriber->setLicenceNumber($csvLine['NO ADHERENT']);
                    }
                }

            // Recherche subscriber dans la saison en cours d'import pour savoir s'il a déjà été enregistré
                $subscription = $this->subscriptionRepository->findOneBy([
                'season' => $season,
                'subscriber' => $subscriber
                ]);

            // Recherche de la licence dans la table licence par le label
            /** @var Licence $licence */
                $licence = $this->licenceRepository->findOneBy(['acronym' => $csvLine['CATEGORIE LICENCE']]);

            // Si on ne trouve pas le subscriber pour la saison, on crée une nouvelle subscription
            // Si on l'a trouvé on vérifie que la date de saisie est la même, si ce n'est pas le cas, on met à jour
            // la catégorie et le type de licence
                if (!$subscription instanceof Subscription) {
                    $subscription = new Subscription();
                    $this->entityManager->persist($subscription);
                    $subscription
                    ->setSeason($season)
                    ->setSubscriber($subscriber)
                    ->setSubscriptionDate($subscriptionDate)
                    ->setLicence($licence)
                    ->setCategory($this->categoryRepository->findOneBy(['label' => $csvLine['CATEGORIE AGE']]));
                    $counts['newSubscriptionsCount']++;
                } elseif ($subscriptionDate > $subscription->getSubscriptionDate()) {
                    $subscription
                    ->setSubscriptionDate($subscriptionDate)
                    ->setLicence($licence)
                    ->setCategory($this->categoryRepository->findOneBy(['label' => $csvLine['CATEGORIE AGE']]));
                }
            }
        }
        $this->entityManager->refresh($season);
        $this->entityManager->flush();

        $this->entityManager->refresh($season);
        $this->removeEmptySeason($season);

        return $counts;
    }

    private function removeEmptySeason(Season $season): void
    {
        if (count($season->getSubscriptions()) === 0) {
            $this->entityManager->remove($season);
            $this->entityManager->flush();
        }
    }

    private function stringToDatetime(string $date): DateTime
    {
        $dateArray = explode('/', $date);
        return (new DateTime())
            ->setDate(
                (int)$dateArray[2],
                (int)$dateArray[1],
                (int)$dateArray[0]
            )
            ->setTime(0, 0, 0);
    }
}
