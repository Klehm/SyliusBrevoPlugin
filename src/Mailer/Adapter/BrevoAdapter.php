<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoPlugin\Mailer\Adapter;

use Klehm\SyliusBrevoPlugin\Mailer\BrevoMailerInterface;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface;
use Sylius\Component\Mailer\Sender\Adapter\CcAwareAdapterInterface;

final class BrevoAdapter implements AdapterInterface, CcAwareAdapterInterface
{
    public function __construct(
        private BrevoMailerInterface $brevoMailer,
    ) {
    }

    public function send(
        array $recipients,
        string $senderAddress,
        string $senderName,
        RenderedEmail $renderedEmail,
        EmailInterface $email,
        array $data,
        array $attachments = [],
        array $replyTo = [],
    ): void {
        $this->brevoMailer->send(
            $recipients,
            $senderAddress,
            $senderName,
            $renderedEmail,
            $email,
            $data,
            $attachments,
            $replyTo,
            [],
            [],
        );
    }

    public function sendWithCc(
        array $recipients,
        string $senderAddress,
        string $senderName,
        RenderedEmail $renderedEmail,
        EmailInterface $email,
        array $data,
        array $attachments = [],
        array $replyTo = [],
        array $ccRecipients = [],
        array $bccRecipients = [],
    ): void {
        $this->brevoMailer->send(
            $recipients,
            $senderAddress,
            $senderName,
            $renderedEmail,
            $email,
            $data,
            $attachments,
            $replyTo,
            $ccRecipients,
            $bccRecipients,
        );
    }
}
