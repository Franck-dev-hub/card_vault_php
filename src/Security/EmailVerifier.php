<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

readonly class EmailVerifier
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface            $mailer,
        private EntityManagerInterface     $entityManager,
        private LoggerInterface            $logger
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void
    {
        $this->logger->info("=== EMAIL VERIFICATION START ===");
        $this->logger->info("Route name: $verifyEmailRouteName");
        $this->logger->info("User ID: " . $user->getId());
        $this->logger->info("User Email: " . $user->getEmail());

        try {
            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                $verifyEmailRouteName,
                (string)$user->getId(),
                (string)$user->getEmail(),
                ['id' => $user->getId()]
            );

            $this->logger->info("Signature generated");

            $context = $email->getContext();
            $context['signedUrl'] = $signatureComponents->getSignedUrl();
            $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
            $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

            $email->context($context);

            $this->logger->info("Email context set");
            $this->logger->info("Sending email to: " . $user->getEmail());

            $this->mailer->send($email);

            $this->logger->info("=== EMAIL SENT SUCCESSFULLY ===");
        } catch (\Exception $e) {
            $this->logger->error("=== EMAIL ERROR ===");
            $this->logger->error("Exception: " . $e->getMessage());
            $this->logger->error("File: " . $e->getFile());
            $this->logger->error("Line: " . $e->getLine());
            throw $e;
        }
    }

    public function handleEmailConfirmation(Request $request, User $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, (string)$user->getId(), (string)$user->getEmail());

        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
