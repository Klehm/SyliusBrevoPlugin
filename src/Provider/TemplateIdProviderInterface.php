<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoPlugin\Provider;

interface TemplateIdProviderInterface
{
    public function getId(string $templateName, string $locale): ?int;

    public function getTemplates(): array;
}
