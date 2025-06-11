<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoPlugin\Event;

use Sylius\Component\Mailer\Model\EmailInterface;
use Symfony\Contracts\EventDispatcher\Event;

class BrevoMailerDataEvent extends Event
{
    public function __construct(
        private EmailInterface $email,
        private array $data,
    ) {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getEmail(): EmailInterface
    {
        return $this->email;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
