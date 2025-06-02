<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoPlugin\Api\Builder;

use Klehm\SyliusBrevoPlugin\Model\HtmlMessage;

interface HtmlMessagePayloadBuilderInterface
{
    public function build(HtmlMessage $message): array;
}
