<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoMailerPlugin\Provider;

interface TemplateIdProviderInterface
{
    public function getId(string $templateName): ?int;
}
