<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(UserInterface $user): Response
    {
        // Récupérer l'email de l'utilisateur
        $userEmail = $user->getEmail();

        // Passer l'email à la vue pour générer un lien de réinitialisation de mot de passe
        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'userEmail' => $userEmail,  // On passe l'email de l'utilisateur à la vue
        ]);
    }
}
