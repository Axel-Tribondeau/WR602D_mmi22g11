<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    public function assignFreeSubscription(User $user, EntityManagerInterface $entityManager): void
    {
        // Trouver l'abonnement gratuit avec l'id = 1 et l'assigner à l'utilisateur
        $subscription = $entityManager->getRepository(Subscription::class)->find(1); // L'ID de l'abonnement gratuit
        if ($subscription) {
            $user->setSubscription($subscription);
        } else {
            // Si l'abonnement n'existe pas, afficher un message d'erreur
            throw new \LogicException('L\'abonnement gratuit n\'a pas été trouvé.');
        }
    }
}
