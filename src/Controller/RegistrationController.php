<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Subscription; // Assure-toi que Subscription est bien importé ici
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
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        // Créer un nouvel utilisateur
        $user = new User();
        // Créer le formulaire d'inscription
        $form = $this->createForm(RegistrationFormType::class, $user);
        // Traiter les données envoyées par le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Encoder le mot de passe
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Assigner prénom et nom à l'utilisateur
            $user->setFirstname($form->get('firstname')->getData());
            $user->setLastname($form->get('lastname')->getData());

            // Trouver l'abonnement gratuit avec l'id = 1 et l'assigner à l'utilisateur
            $subscription = $entityManager->getRepository(Subscription::class)->find(1); // L'ID de l'abonnement gratuit
            if ($subscription) {
                $user->setSubscription($subscription);
            } else {
                // Si l'abonnement n'existe pas, afficher un message d'erreur
                $this->addFlash('error', 'L\'abonnement gratuit n\'a pas été trouvé.');
                return $this->redirectToRoute('app_register');
            }

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

        // Si le formulaire n'est pas valide, ou s'il n'est pas soumis, on le rend à l'utilisateur
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Valider le lien de confirmation de l'email, cela met à jour `User::isVerified=true` et persiste l'utilisateur
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
