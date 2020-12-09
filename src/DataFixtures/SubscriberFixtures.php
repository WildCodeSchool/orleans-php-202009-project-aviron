<?php

namespace App\DataFixtures;

use App\Entity\Subscriber;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class SubscriberFixtures extends Fixture
{
    private const GENDER = [
        'F',
        'H',
    ];

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++) {
            $subscriber = new Subscriber();
            $subscriber->setFirstname($faker->firstName);
            $subscriber->setLastname($faker->lastName);
            $subscriber->setBirthdate(\DateTime::createFromFormat(
                'Y-m-d',
                $faker->dateTimeThisCentury->format('Y-m-d')
            ));
            $subscriber->setLicenceNumber($faker->randomNumber(5, false));
            $subscriber->setGender(self::GENDER[rand(0, 1)]);

            $manager->persist($subscriber);
            $this->addReference('subscriber_' . $i, $subscriber);
        }
        $manager->flush();
    }
}
