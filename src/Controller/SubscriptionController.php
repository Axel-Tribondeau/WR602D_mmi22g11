<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SubscriptionController extends AbstractController
{
    // Route pour la page des abonnements
    #[Route('/subscriptions', name: 'app_subscriptions')]
    public function index(): Response
    {
        // Exemple de données pour les abonnements
        $subscriptions = [
            [
                'name' => 'Gratuit',
                'price' => '0€',
                'limit' => '2 PDF par jour',
                'features' => ['Accès à la plateforme', '2 PDF par jour'],
            ],
            [
                'name' => 'Abonnement Standard',
                'price' => '9.99€',
                'limit' => '10 PDF par jour',
                'features' => ['Accès à la plateforme', '10 PDF par jour', 'Support client standard'],
            ],
            [
                'name' => 'Abonnement Premium',
                'price' => '19.99€',
                'limit' => 'PDF illimités',
                'features' => ['Accès à la plateforme', 'PDF illimités par jour', 'Support client prioritaire', 'Sans publicités'],
            ],
        ];

        // Rendu de la vue avec les abonnements
        return $this->render('subscription/index.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }
}
