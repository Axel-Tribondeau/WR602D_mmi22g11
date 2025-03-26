<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SubscriptionController extends AbstractController
{
    #[Route('/subscriptions', name: 'app_subscriptions')]
    public function index(SubscriptionRepository $subscriptionRepository): Response
    {
        $subscriptions = $subscriptionRepository->findAll();

        return $this->render('subscription/index.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }

    #[Route('/change-subscription/{name}', name: 'app_change_subscription')]
    public function changeSubscription(
        string $name,
        SubscriptionRepository $subscriptionRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_register');
        }

        $subscription = $subscriptionRepository->findOneBy(['name' => $name]);

        if (!$subscription) {
            $this->addFlash('error', 'L\'abonnement sélectionné n\'existe pas.');
            return $this->redirectToRoute('app_subscriptions');
        }

        $user->setSubscription($subscription);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Votre abonnement a été changé avec succès !');
        return $this->redirectToRoute('app_subscriptions');
    }
}
