<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoPlugin\Api\Builder;

use Klehm\SyliusBrevoPlugin\Model\TemplateMessage;

final class TemplateMessagePayloadBuilder implements TemplateMessagePayloadBuilderInterface
{
    public function build(TemplateMessage $message): array
    {
        $output = [
            'sender' => $message->getFrom(),
            'to' => $message->getTo(),
            'replyTo' => $message->getReplyTo(),
            'cc' => $message->getCc(),
            'bcc' => $message->getBcc(),
            'params' => $message->getData(),
            'attachment' => $message->getAttachments(),
        ];

        $output = array_filter($output, static fn ($value) => $value !== null && $value !== []);

        $output['templateId'] = $message->getTemplateId();

        return $output;
    }
}
