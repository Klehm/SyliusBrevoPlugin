<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoPlugin\Api\Builder;

use Klehm\SyliusBrevoPlugin\Model\HtmlMessage;

final class HtmlMessagePayloadBuilder implements HtmlMessagePayloadBuilderInterface
{
    public function build(HtmlMessage $message): array
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

        $output['subject'] = $message->getSubject();
        $output['htmlContent'] = $message->getHtmlContent();

        return $output;
    }
}
