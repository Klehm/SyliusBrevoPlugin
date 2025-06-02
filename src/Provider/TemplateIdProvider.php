<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoPlugin\Provider;

class TemplateIdProvider implements TemplateIdProviderInterface
{
    private array $templates;

    public function __construct(array $templates)
    {
        $this->templates = $templates;
    }

    public function getId(string $templateName, string $locale): ?int
    {
        $templates = $this->templates[$locale] ?? [];
        if (!isset($templates[$templateName])) {
            return null;
        }

        if (!is_int($templates[$templateName])) {
            throw new \InvalidArgumentException(sprintf('Template ID for "%s" in locale "%s" must be an integer.', $templateName, $locale));
        }

        return $templates[$templateName];
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }
}
