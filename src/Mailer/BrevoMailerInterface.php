<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoMailerPlugin\Mailer;

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;

interface BrevoMailerInterface
{
    /**
     * Sends an email using the Brevo API.
     * This method handles the preparation and sending of the email,
     * including setting the sender, recipients, and any additional data.
     *
     * If a template ID is available for the email code,
     * it will use the template to send the email.
     * Otherwise, it will send a standard HTML email with the provided content.
     *
     * @param array $recipients List of recipient email addresses.
     * @param string $senderAddress The sender's email address.
     * @param string $senderName The sender's name.
     * @param RenderedEmail $renderedEmail The rendered email content.
     * @param EmailInterface $email The email model containing metadata.
     * @param array $data Additional data to be sent with the email.
     * @param array $attachments Optional attachments to include in the email.
     * @param array $replyTo Optional reply-to addresses.
     * @param array $ccRecipients Optional CC recipients.
     * @param array $bccRecipients Optional BCC recipients.
     */
    public function send(
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
    ): void;
}
