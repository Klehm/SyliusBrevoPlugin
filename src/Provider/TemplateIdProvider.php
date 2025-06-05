<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoMailerPlugin\Provider;

class TemplateIdProvider implements TemplateIdProviderInterface
{
    private array $templates;

    public function __construct(array $templates)
    {
        $this->templates = $templates;
    }

    public function getId(string $templateName): ?int
    {
        return $this->templates[$templateName] ?? null;
    }
}
