<?php

namespace App\Service;

use App\Entity\Season;
use App\Entity\Status;
use App\Entity\Subscriber;
use App\Entity\Subscription;
use App\Repository\StatusRepository;
use App\Repository\SubscriberRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */

class StatusCalculator
{
    /**
     * @var StatusRepository
     */
    private StatusRepository $statusRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var SubscriptionRepository
     */
    private SubscriptionRepository $subscriptionRepository;
    /**
     * @var SubscriberRepository
     */
    private SubscriberRepository $subscriberRepository;

    /**
     * StatusCalculator constructor.
     * @param EntityManagerInterface $entityManager
     * @param StatusRepository $statusRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param SubscriberRepository $subscriberRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        StatusRepository $statusRepository,
        SubscriptionRepository $subscriptionRepository,
        SubscriberRepository $subscriberRepository
    ) {
        $this->entityManager = $entityManager;
        $this->statusRepository = $statusRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->subscriberRepository = $subscriberRepository;
    }

    /**
     * @param array<Season> $seasons
     */
    public function calculate(array $seasons): void
    {
        for ($i = 0; $i < count($seasons); $i++) {
            // Si on est sur la première saison importée, on passe toutes les inscriptions en nouveau (à valider avec
            // Patrick, sinon ce sera null car on ne peut pas calculer un statut sans données de la saison précédente)
            if ($i === 0) {
                $subscriptions = $seasons[$i]->getSubscriptions();
                foreach ($subscriptions as $subscription) {
                    $subscription->setStatus($this->statusRepository->findOneBy(['label' => 'N']));
                }
            } else {
                // Si ce n'est pas la première saison, on stocke la saison précédente et son dernier numéro de licence
                // dans des variables
                $previousSeason = $seasons[$i - 1];
                $previousSeasonLastLicence = $this
                    ->subscriptionRepository
                    ->getLastSubscriber($previousSeason->getName())[0]['licenceNumber'];

                // On récupère toutes les inscriptions de la saison en cours de calcul
                $subscriptionsSeason = $seasons[$i]->getSubscriptions();

                // On boucle sur chaque inscription pour calculer le statut en fonction des saisons précédentes :

                // CAS 1 : le n° de licence est supérieur au dernier numéro de licence de la saison précedente : on en
                // déduit que c'est un NOUVEAU

                // SINON CAS 2 : On cherche le rameur dans les inscriptions de la saison précédente,
                // si on le trouve, c'est un RENOUVELLEMENT

                // SINON CAS 3 : On cherche une inscription correspondant à une saison plus ancienne

                // SINON DERNIER CAS : c'est un TRANSFERT

                foreach ($subscriptionsSeason as $subscription) {
                    $subscriber = $subscription->getSubscriber();

                    if ($subscriber->getLicenceNumber() > $previousSeasonLastLicence) {
                        $subscription->setStatus($this->statusRepository->findOneBy(['label' => 'N']));
                    } elseif (
                        $this->subscriptionRepository->findOneBy([
                        'subscriber' => $subscriber,
                        'season' => $previousSeason
                        ])
                    ) {
                        $subscription->setStatus($this->statusRepository->findOneBy(['label' => 'R']));
                    } elseif ($this->hasPreviousSubscription($subscriber, $seasons, $i)) {
                            $subscription->setStatus($this->statusRepository->findOneBy(['label' => 'P']));
                    } else {
                        $subscription->setStatus($this->statusRepository->findOneBy(['label' => 'T']));
                    }
                }
            }
            $this->entityManager->flush();
        }
    }

    private function hasPreviousSubscription(Subscriber $subscriber, array $seasons, int $seasonIndex): bool
    {
        $previousSubscription = false;
        for ($j = 0; $j < $seasonIndex - 1; $j++) {
            if (
                $this->subscriptionRepository->findOneBy([
                'subscriber' => $subscriber,
                'season' => $seasons[$j]
                ])
            ) {
                $previousSubscription = true;
            }
        }

        return $previousSubscription;
    }
}
