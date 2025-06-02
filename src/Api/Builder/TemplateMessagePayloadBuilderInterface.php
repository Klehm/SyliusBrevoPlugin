<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoPlugin\Api\Builder;

use Klehm\SyliusBrevoPlugin\Model\TemplateMessage;

interface TemplateMessagePayloadBuilderInterface
{
    public function build(TemplateMessage $message): array;
}
