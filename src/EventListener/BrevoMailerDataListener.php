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

        if (array_key_exists('localeCode', $data)) {
            $data['locale'] = $data['localeCode'];
        }

        if (array_key_exists('channel', $data)) {
            $data['channel_hostname'] = $data['channel']->getHostname();
            $data['channel_name'] = $data['channel']->getName();
            $data['channel_color'] = $data['channel']->getColor();
        }

        $data['subject'] = $email->getSubject() ?? '';

        $event->setData($data);
    }
}
