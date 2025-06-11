# Installation

1. Run `composer require klehm/sylius-brevo-plugin`.

2. Add the plugin to the `config/bundles.php` file if not already done automatically:

   ```php
   Klehm\SyliusBrevoPlugin\KlehmSyliusBrevoPlugin::class => ['all' => true],
   ```

3. Add the plugin's configuration by creating the file `config/packages/klehm_sylius_brevo.yaml` with the following content:

   ```yaml
    klehm_sylius_brevo:
        api_key: '%env(BREVO_API_KEY)%'
   ```

4. Setup the Brevo mailer adapter by creating (or updating) the file `config/packages/sylius_mailer.yaml` with:
    ```yaml
    sylius_mailer:
        sender_adapter: klehm_sylius_brevo.sender_adapter
    ```

5. Configure Brevo API key 
    Create an API key in your account by following the [official documentation](https://help.brevo.com/hc/en-us/articles/209467485-Create-and-manage-your-API-keys#h_01GW6ZQEKZ072SFGK03N9R6VE6).
    ```dotenv
    # .env.local

    ...
    BREVO_API_KEY=YOUR_KEY
    ```

    That's it! All emails are sent through Brevo.

6. (optional) If you want to use Brevo transactional templates, follow this example (`config/packages/klehm_sylius_brevo.yaml`):

   ```yaml
   klehm_sylius_brevo:
        api_key: '%env(BREVO_API_KEY)%'
        templates:
            en_US:
                user_registration: 1
                contact_request: 2
            fr_FR:
                contact_request: 3
   ```

   To enable you to configure and customize Brevo templates, follow [this documentation](docs/templating.md).

## Requirements:
We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.

| Package       | Version |
|---------------|---------|
| PHP           | \>=8.2  |
| sylius/sylius | 2.0.x   |
