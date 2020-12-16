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
        'female' => 'F',
        'male' => 'H',
    ];

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++) {
            $subscriber = new Subscriber();
            $gender = array_rand(self::GENDER, 1);
            $subscriber->setGender(self::GENDER[$gender]);
            $subscriber->setFirstname($faker->firstName($gender));
            $subscriber->setLastname($faker->lastName);
            $subscriber->setBirthdate($faker->dateTimeThisCentury());
            $subscriber->setLicenceNumber($i + 1);

            $manager->persist($subscriber);
            $this->addReference('subscriber_' . $i, $subscriber);
        }
        $manager->flush();
    }
}
