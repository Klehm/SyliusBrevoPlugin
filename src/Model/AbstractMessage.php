<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoMailerPlugin\Model;

abstract class AbstractMessage
{
    protected ?array $from = null;

    protected ?array $to = null;

    protected ?array $replyTo = null;

    protected ?array $cc = null;

    protected ?array $bcc = null;

    protected array $data = [];

    protected array $attachments = [];

    public function getFrom(): ?array
    {
        return $this->from;
    }

    public function setFrom(?array $from): static
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): ?array
    {
        return $this->to;
    }

    public function setTo(?array $to): static
    {
        $this->to = $to;

        return $this;
    }

    public function getReplyTo(): ?array
    {
        return $this->replyTo;
    }

    public function setReplyTo(?array $replyTo): static
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    public function getCc(): ?array
    {
        return $this->cc;
    }

    public function setCc(?array $cc): static
    {
        $this->cc = $cc;

        return $this;
    }

    public function getBcc(): ?array
    {
        return $this->bcc;
    }

    public function setBcc(?array $bcc): static
    {
        $this->bcc = $bcc;

        return $this;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function setAttachments(array $attachments): static
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
