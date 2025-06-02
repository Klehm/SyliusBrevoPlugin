<?php

declare(strict_types=1);

namespace Tests\Klehm\SyliusBrevoPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\Then;
use Klehm\SyliusBrevoPlugin\Api\BrevoApiClientInterface;
use Klehm\SyliusBrevoPlugin\Provider\TemplateIdProviderInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\Klehm\SyliusBrevoPlugin\Behat\Fake\AlwaysSuccessBrevoClient;

final class BrevoContext implements Context
{
    public function __construct(
        private string $brevoApiKey,
        private TemplateIdProviderInterface $templateIdProvider,
        private BrevoApiClientInterface $brevoApiClient,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    #[Given('I have set up the Brevo API Key')]
    public function iHaveSetUpTheBrevoApiKey(): void
    {
        if (empty($this->brevoApiKey)) {
            throw new \RuntimeException('Brevo API Key is not set.');
        }
    }

    #[Given('I have setup the :code template in Brevo')]
    public function iHaveSetupTheTemplateInBrevo(string $code): void
    {
        $channel = $this->sharedStorage->get('channel');
        $templateId = $this->templateIdProvider->getId($code, $channel->getDefaultLocale()->getCode());
        if (empty($templateId)) {
            throw new \RuntimeException(sprintf('Template "%s" is not set up in Brevo.', $code));
        }
    }

    #[Given('I haven\'t setup the :code template in Brevo')]
    public function iHaventSetupTheTemplateInBrevo(string $code): void
    {
        $channel = $this->sharedStorage->get('channel');
        $templateId = $this->templateIdProvider->getId($code, $channel->getDefaultLocale()->getCode());
        if (!empty($templateId)) {
            throw new \RuntimeException(sprintf('Template "%s" is set up in Brevo.', $code));
        }
    }

    #[Then('there should be this a raw email sent to :email in Brevo logs')]
    public function thereShouldBeThisARawEmailSentToInBrevoLogs(string $email): void
    {
        if (!($this->brevoApiClient instanceof AlwaysSuccessBrevoClient)) {
            sleep(30); // Wait for the email to be processed in Brevo
        }

        $this->getLastEmailByEmail($email);
    }

    #[Then('there should be this template :code email sent to :email in Brevo logs')]
    public function thereShouldBeThisTemplateEmailSentToInBrevoLogs(string $code, string $email): void
    {
        $channel = $this->sharedStorage->get('channel');
        $templateId = $this->templateIdProvider->getId($code, $channel->getDefaultLocale()->getCode());
        if (empty($templateId)) {
            throw new \RuntimeException(sprintf('Template "%s" is not set up in Brevo.', $code));
        }

        if (!($this->brevoApiClient instanceof AlwaysSuccessBrevoClient)) {
            sleep(60); // Wait for the email to be processed in Brevo
        }

        $lastEmail = $this->getLastEmailByEmail($email);
        if (!isset($lastEmail['templateId']) || (int) $lastEmail['templateId'] !== $templateId) {
            throw new \RuntimeException(sprintf('The last email sent to "%s" does not match the template "%s".', $email, $code));
        }
    }

    private function getLastEmailByEmail(string $email): array
    {
        $emails = $this->brevoApiClient->getTransactionalEmailsByEmail($email);
        if (empty($emails)) {
            throw new \RuntimeException(sprintf('No emails found for email address "%s".', $email));
        }

        return $emails[0]; // Return the most recent email
    }
}
