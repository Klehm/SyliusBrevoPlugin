<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoMailerPlugin\Model;

class HtmlMessage extends AbstractMessage
{
    protected string $htmlContent;

    protected string $subject;

    public function getHtmlContent(): string
    {
        return $this->htmlContent;
    }

    public function setHtmlContent(string $htmlContent): static
    {
        $this->htmlContent = $htmlContent;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }
}
