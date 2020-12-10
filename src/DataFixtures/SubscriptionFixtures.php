<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class SubscriptionFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 200; $i++) {
            $subscription = new Subscription();
            $subscription->setSubscriptionDate($faker->dateTimeThisDecade('now'));
            $subscription->setSubscriber($this->getReference('subscriber_' . rand(0, 99)));
            $subscription->setSeason($this->getReference('season_' . rand(0, 6)));
            $subscription->setLicence($this->getReference('licence_' . rand(0, 5)));
            $manager->persist($subscription);
            $this->addReference('subscription_' . $i, $subscription);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [SubscriberFixtures::class, SeasonFixtures::class, LicenceFixtures::class];
    }
}
