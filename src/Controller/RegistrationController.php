<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Service\MenuService;
use App\Service\LanguageManager;
use App\Service\PokemonService;
use App\Service\UserPreferencesService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends BaseController
{
    public function __construct(
        MenuService                      $footerService,
        UserPreferencesService           $userPreferencesService,
        TranslatorInterface              $translator,
        LanguageManager                  $languageManager,
        PokemonService                   $pokemonService,
        protected readonly EmailVerifier $emailVerifier,
        private readonly string          $emailFromAddress,
        private readonly string          $emailFromName,
    )
    {
        parent::__construct(
            $footerService,
            $translator,
            $userPreferencesService,
            $languageManager,
            $pokemonService);
    }

    private const string PAGE_NAME = "register";

    /**
     * @throws TransportExceptionInterface|InvalidArgumentException
     */
    #[Route("/" . self::PAGE_NAME, name: self::PAGE_NAME)]
    public function register(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface      $entityManager
    ): Response
    {
        // Redirect user if logged in
        if ($this->getUser()) {
            return $this->redirectToRoute("root");
        }
        
        $user = new User();
        $locale = $this->languageManager->getAppLanguage();
        $this->translator->setLocale($locale);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get("plainPassword")->getData();
            $username = $form->get("username")->getData();

            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setUsername($username);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                new TemplatedEmail()
                    ->from(new Address($this->emailFromAddress, $this->emailFromName))
                    ->to((string)$user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            $this->addFlash('success', 'Please check your email to verify your account');
            return $this->redirectToRoute("login");
        }

        return $this->renderPage("register.html.twig", [
            "dir" => "registration",
            "currentPage" => self::PAGE_NAME,
            "registrationForm" => $form->createView()
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('register');
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error',
                $this->translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('register');
        }

        $this->addFlash('success', 'Your email address has been verified.');
        return $this->redirectToRoute('login');
    }
}
