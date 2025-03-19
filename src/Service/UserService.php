<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function createUser(string $plainPassword): User
    {
        $user = new User();
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));

        // Trouver l'abonnement gratuit (ID = 1)
        $subscription = $this->entityManager->getRepository(Subscription::class)->find(1);
        if ($subscription) {
            $user->setSubscription($subscription);
        }

        return $user;
    }
}
