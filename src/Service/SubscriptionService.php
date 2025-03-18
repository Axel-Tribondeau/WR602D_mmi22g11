<?php

namespace App\Service;

use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionService
{
private $entityManager;

public function __construct(EntityManagerInterface $entityManager)
{
$this->entityManager = $entityManager;
}

/**
* Récupérer l'abonnement gratuit avec l'ID 1
*
* @return Subscription|null
*/
public function getFreeSubscription(): ?Subscription
{
return $this->entityManager->getRepository(Subscription::class)->find(1); // ID de l'abonnement gratuit
}
}
