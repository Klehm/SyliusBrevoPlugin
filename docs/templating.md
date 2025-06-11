# Templating

By default, the existing Twig templates are using.<br />
If you want to use Brevo template editor (for you or your client), the plugin allows you to have an hybrid configuration.<br/>


## Configure templates mapping
Edit the file (`config/packages/klehm_sylius_brevo.yaml`):
   ```yaml
   klehm_sylius_brevo:
        ...
        templates:
            en_US:
                user_registration: 1
                contact_request: 2
            fr_FR:
                contact_request: 3
   ```

To find the Brevo template IDs, run this command :
```shell
php bin/console brevo:transactional-templates:list
```


## Params
The plugin send 5 params with all send requests : 
- `locale` -> locale code (ex: en_US)
- `channel_hostname` -> the context channel hostname (ex: example.com)
- `channel_name` -> the context channel name (ex: My Store)
- `channel_color` -> the context channel color code (ex: #ffffff)
- `subject` -> the initial mail subject (ex: "Welcome to the store !")

## Params customization
You can expose extra params by listening 2 events :
```yaml
# General event (all emails): 
klehm_sylius_brevo_plugin.mailer_data

# Specific event :
klehm_sylius_brevo_plugin.mailer_data.%email_code% # ex: klehm_sylius_brevo_plugin.mailer_data.user_registration
```

For a complete example : 
```yaml
Klehm\SyliusBrevoPlugin\EventListener\BrevoMailerDataListener:
    arguments: []
    tags:
        - { name: 'kernel.event_listener', event: 'klehm_sylius_brevo_plugin.mailer_data', method: 'onMailerData' }
```

```php
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
```
