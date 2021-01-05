<?php

namespace App\Service;

use App\Entity\Import;
use App\Entity\Subscriber;
use App\Repository\SeasonRepository;
use App\Repository\SubscriberRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CsvParser
    /**
     * @SuppressWarnings(PHPMD.LongVariable)
     */
{
    public function getDataFromCsv(File $import): array
    {
        $encoders = [new CsvEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $csvString = file_get_contents($import);

        return $serializer->decode($csvString ?? '', 'csv', [
            'csv_delimiter' => ';',
        ]);
    }
}
