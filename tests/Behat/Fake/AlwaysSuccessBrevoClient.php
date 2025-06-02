<?php

declare(strict_types=1);

namespace Tests\Klehm\SyliusBrevoPlugin\Behat\Fake;

use Klehm\SyliusBrevoPlugin\Api\BrevoApiClient;
use Klehm\SyliusBrevoPlugin\Model\HtmlMessage;
use Klehm\SyliusBrevoPlugin\Model\TemplateMessage;

final class AlwaysSuccessBrevoClient extends BrevoApiClient
{
    public function sendEmailWithTemplate(TemplateMessage $message): ?string
    {
        return 'fake-message-id';
    }

    public function sendHtmlEmail(HtmlMessage $message): ?string
    {
        return 'fake-message-id';
    }

    public function getTemplates(): array
    {
        return [
            ['id' => 1, 'name' => 'Test Template', 'subject' => 'Test Subject'],
            ['id' => 2, 'name' => 'Another Template', 'subject' => 'Another Subject'],
        ];
    }

    public function getTransactionalEmailsByEmail(string $email): array
    {
        return [
            [
                'templateId' => 1,
            ],
        ];
    }
}
