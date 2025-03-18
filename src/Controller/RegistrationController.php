<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Subscription;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Service\UserService;  // Nouvelle dépendance ajoutée pour séparer la logique métier

class RegistrationController extends AbstractController
{
    private $emailVerifier;
    private $userService;

    // Injection des dépendances via le constructeur
    public function __construct(EmailVerifier $emailVerifier, UserService $userService)
    {
        $this->emailVerifier = $emailVerifier;
        $this->userService = $userService;  // Service qui gère la logique utilisateur
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {

        // Créer un nouvel utilisateur
        $user = new User();
        // Créer le formulaire d'inscription
        $form = $this->createForm(RegistrationFormType::class, $user);
        // Traiter les données envoyées par le formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Traiter le mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Assigner prénom et nom à l'utilisateur
            $user->setFirstname($form->get('firstname')->getData());
            $user->setLastname($form->get('lastname')->getData());

            // Assigner l'abonnement gratuit à l'utilisateur
            $this->userService->assignFreeSubscription($user, $entityManager);

            // Sauvegarder l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Créer un email de confirmation
            $email = (new TemplatedEmail())
                ->from(new Address('axeltribondeau@gmail.com', 'PDF Mail Bot'))
                ->to((string) $user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig');

            // Envoyer l'email de confirmation
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user, $email);

            // Connexion automatique de l'utilisateur après l'inscription
            return $security->login($user, 'form_login', 'main');
        }

        // Si le formulaire n'est pas valide ou n'est pas soumis, on le rend à l'utilisateur
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Valider le lien de confirmation de l'email
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            // En cas d'erreur, rediriger l'utilisateur vers la page d'inscription
            return $this->redirectToRoute('app_register');
        }

        // Afficher un message de succès et rediriger
        $this->addFlash('success', 'Votre adresse e-mail a été vérifiée.');
        return $this->redirectToRoute('app_register');
    }
}
