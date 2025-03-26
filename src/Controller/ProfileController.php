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
        $userEmail = $user->getEmail();

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'userEmail' => $userEmail,
        ]);
    }
}
