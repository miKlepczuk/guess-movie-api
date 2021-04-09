<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class PasswordRecoveryManager
{

    public function __construct(
        private MailerInterface $mailer,
        private ContainerBagInterface $params,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function recoverPassword($user): bool
    {
        $this->generateRecoveryKey($user);
        $recoveryLink = $this->generateRecoveryLink($user);

        $senderEmail = $this->params->get('app.mailer_sender_email');
        $senderName = $this->params->get('app.mailer_sender_name');

        $recipient = $user->getEmail();

        $message = (new TemplatedEmail())
            ->from(new Address($senderEmail, $senderName))
            ->to($recipient)
            ->subject('Password Recovery')
            ->htmlTemplate('emails/recover-password.html.twig')
            ->context([
                'recoveryLink' => $recoveryLink,
                'recipient_email' => $recipient,
            ]);

        $this->mailer->send($message);

        return true;
    }

    private function generateRecoveryKey($user)
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $key = [];
        $alphabetLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 20; $i++) {
            $rand = rand(0, $alphabetLength);
            $key[] = $alphabet[$rand];
        }
        $recoveryKey = implode($key);

        $user->setRecoveryKey($recoveryKey);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $recoveryKey;
    }

    private function generateRecoveryLink($user)
    {
        $domain = $_ENV["FRONTEND_URL"];

        $recoveryKey = $user->getRecoveryKey();

        return  $domain . "/reset-password?recovery_key=" . $recoveryKey;
    }
}
