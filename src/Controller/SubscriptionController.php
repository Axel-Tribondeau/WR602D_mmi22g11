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
    // Route pour la page des abonnements
    #[Route('/subscriptions', name: 'app_subscriptions')]
    public function index(SubscriptionRepository $subscriptionRepository): Response
    {
        // Récupérer toutes les données de la table subscriptions
        $subscriptions = $subscriptionRepository->findAll();

        // Rendu de la vue avec les abonnements récupérés
        return $this->render('subscription/index.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }

    // Route pour changer l'abonnement de l'utilisateur
    #[Route('/change-subscription/{id}', name: 'app_change_subscription')]
    public function changeSubscription(
        int $id,
        SubscriptionRepository $subscriptionRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer l'utilisateur connecté avec getUser()
        $user = $this->getUser();

        // Vérifier si l'utilisateur est authentifié
        if (!$user instanceof User) {
            // Si l'utilisateur n'est pas authentifié, le rediriger vers la page d'inscription
            return $this->redirectToRoute('app_register');
        }

        // Trouver l'abonnement correspondant à l'id
        $subscription = $subscriptionRepository->find($id);

        // Si l'abonnement n'existe pas, afficher un message d'erreur
        if (!$subscription) {
            $this->addFlash('error', 'L\'abonnement sélectionné n\'existe pas.');
            return $this->redirectToRoute('app_subscriptions');
        }

        // Modifier l'abonnement de l'utilisateur
        $user->setSubscription($subscription);

        // Enregistrer la modification dans la base de données
        $entityManager->persist($user);
        $entityManager->flush();

        // Ajouter un message de succès et rediriger vers la page des abonnements
        $this->addFlash('success', 'Votre abonnement a été changé avec succès !');
        return $this->redirectToRoute('app_subscriptions');
    }
}
