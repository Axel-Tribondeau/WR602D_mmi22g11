<?php

// src/Service/UserService.php
namespace App\Service;

use App\Entity\User;
use App\Service\SubscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private $entityManager;
    private $passwordHasher;
    private $subscriptionService;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        SubscriptionService $subscriptionService
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Crée un utilisateur et lui assigne un abonnement gratuit
     *
     * @param User $user
     * @param string $plainPassword
     * @return void
     */
    public function createUserWithFreeSubscription(User $user, string $plainPassword): void
    {
        // Encodage du mot de passe
        $encodedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($encodedPassword);

        // Assignation de l'abonnement gratuit
        $subscription = $this->subscriptionService->getFreeSubscription();
        if ($subscription) {
            $user->setSubscription($subscription);
        } else {
            throw new \Exception('L\'abonnement gratuit n\'a pas été trouvé.');
        }

        // Sauvegarde de l'utilisateur
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
