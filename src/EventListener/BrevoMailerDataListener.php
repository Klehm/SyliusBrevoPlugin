<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoPlugin\EventListener;

use Klehm\SyliusBrevoPlugin\Event\BrevoMailerDataEvent;
use Sylius\Component\Mailer\Model\EmailInterface;

final class BrevoMailerDataListener
{
    public function onMailerData(BrevoMailerDataEvent $event): void
    {
        /** @var array $data */
        $data = $event->getData();

        /** @var EmailInterface $email */
        $email = $event->getEmail();

        $data['subject'] = $email->getSubject() ?? '';

        $event->setData($data);
    }
}
