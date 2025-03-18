<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Subscription;

class SubscriptionService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function assignFreeSubscription($user)
    {
        // Trouver l'abonnement gratuit (ID fixé à 1)
        $subscription = $this->entityManager->getRepository(Subscription::class)->find(1);

        if ($subscription) {
            // Assigner l'abonnement gratuit à l'utilisateur
            $user->setSubscription($subscription);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return true;  // Si l'abonnement a été assigné avec succès
        }

        return false;  // Si l'abonnement n'a pas été trouvé
    }
}
