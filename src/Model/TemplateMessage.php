<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoPlugin\Model;

class TemplateMessage extends AbstractMessage
{
    protected int $templateId;

    public function getTemplateId(): int
    {
        return $this->templateId;
    }

    public function setTemplateId(int $templateId): static
    {
        $this->templateId = $templateId;

        return $this;
    }
}
