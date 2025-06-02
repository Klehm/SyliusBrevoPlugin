<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoPlugin\Mailer;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Klehm\SyliusBrevoPlugin\Api\BrevoApiClientInterface;
use Klehm\SyliusBrevoPlugin\Model\HtmlMessage;
use Klehm\SyliusBrevoPlugin\Model\TemplateMessage;
use Klehm\SyliusBrevoPlugin\Provider\TemplateIdProviderInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

class BrevoMailer implements BrevoMailerInterface
{
    public function __construct(
        private TemplateIdProviderInterface $templateIdProvider,
        private BrevoApiClientInterface $brevoApiClient,
        private EventDispatcherInterface $dispatcher,
        private LocaleContextInterface $locale,
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
        array $ccRecipients = [],
        array $bccRecipients = [],
    ): void {
        Assert::allStringNotEmpty($recipients);
        Assert::allStringNotEmpty($replyTo);

        $templateId = $this->templateIdProvider->getId($email->getCode() ?? '', $this->locale->getLocaleCode());
        $replyTo = count($replyTo) > 0 ? $this->formatRecipients($replyTo) : ['name' => $senderName, 'email' => $senderAddress];

        if ($templateId) {
            $message = (new TemplateMessage())
                ->setFrom(['name' => $senderName, 'email' => $senderAddress])
                ->setTo($this->formatRecipients($recipients))
                ->setReplyTo($replyTo)
                ->setCc($this->formatRecipients($ccRecipients))
                ->setBcc($this->formatRecipients($bccRecipients))
                ->setAttachments($this->formatAttachments($attachments))
                ->setTemplateId($templateId)
                ->setData($data);

            $emailSendEvent = new EmailSendEvent($message, $email, $data, $recipients, $replyTo);
            $this->dispatcher->dispatch($emailSendEvent, SyliusMailerEvents::EMAIL_PRE_SEND);

            $this->brevoApiClient->sendEmailWithTemplate($message);
        } else {
            $message = (new HtmlMessage())
                ->setFrom(['name' => $senderName, 'email' => $senderAddress])
                ->setTo($this->formatRecipients($recipients))
                ->setReplyTo($replyTo)
                ->setCc($this->formatRecipients($ccRecipients))
                ->setBcc($this->formatRecipients($bccRecipients))
                ->setAttachments($this->formatAttachments($attachments))
                ->setSubject($renderedEmail->getSubject())
                ->setHtmlContent($renderedEmail->getBody());

            $emailSendEvent = new EmailSendEvent($message, $email, $data, $recipients, $replyTo);
            $this->dispatcher->dispatch($emailSendEvent, SyliusMailerEvents::EMAIL_PRE_SEND);

            $this->brevoApiClient->sendHtmlEmail($message);
        }

        $this->dispatcher->dispatch($emailSendEvent, SyliusMailerEvents::EMAIL_POST_SEND);
    }

    /**
     * There are two kinds of recipient array syntax that can be passed to the send method:
     * - Only email addresses: ['john.doe@mail.com', 'jane.smith@mail.com']
     * - Email addresses with names: ['john.doe@mail.com' => 'John Doe', 'jane.smith@mail.com' => 'Jane Smith']
     */
    protected function formatRecipients(array $recipients): array
    {
        $transformedRecipients = [];
        $validator = new EmailValidator();
        foreach ($recipients as $addressOrKey => $nameOrAddress) {
            if (\is_string($addressOrKey) && $validator->isValid($addressOrKey, new RFCValidation())) {
                $transformedRecipients[] = [
                    'name' => $nameOrAddress,
                    'email' => $addressOrKey,
                ];

                continue;
            }

            $transformedRecipients[] = [
                'email' => $nameOrAddress,
            ];
        }

        return $transformedRecipients;
    }

    /**
     * Formats attachments for the Brevo API.
     */
    protected function formatAttachments(array $attachments): array
    {
        $formattedAttachments = [];
        foreach ($attachments as $attachment) {
            if (isset($attachment['filePath'])) {
                $content = file_get_contents($attachment['filePath']);
                if ($content === false) {
                    throw new \RuntimeException(sprintf('Could not read file at path: %s', $attachment['filePath']));
                }
                $formattedAttachments[] = [
                    'name' => $attachment['fileName'] ?? basename($attachment['filePath']),
                    'content' => base64_encode($content),
                ];
            } elseif (isset($attachment['content'])) {
                $formattedAttachments[] = [
                    'name' => $attachment['fileName'] ?? 'attachment',
                    'content' => base64_encode($attachment['content']),
                ];
            }
        }

        return $formattedAttachments;
    }
}
