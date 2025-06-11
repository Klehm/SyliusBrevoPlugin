# Contributing

To contribute you need to:

1. Clone this repository into your development environment

2. [OPTIONAL] Copy the `.env` file inside the test application directory to the `.env.local` file:

   ```bash
   cp tests/Application/.env tests/Application/.env.local
   ```

   Then edit the `tests/Application/.env.local` file by setting configuration specific for you development environment.

3. Then, from the plugin's root directory, run the following commands:

   ```bash
   (cd tests/Application && yarn install)
   (cd tests/Application && yarn build)
   (cd tests/Application && APP_ENV=test bin/console assets:install public)
   (cd tests/Application && APP_ENV=test bin/console doctrine:database:create)
   (cd tests/Application && APP_ENV=test bin/console doctrine:schema:create)
   ```
4. Run test application's webserver on `127.0.0.1:8080`:

      ```bash
      symfony server:ca:install
      APP_ENV=test symfony server:start --port=8080 --dir=tests/Application/public --daemon
      ```

4. Now at https://127.0.0.1:8080/ you have a full Sylius testing application which runs the plugin

### Testing

After your changes you must ensure that the tests are still passing. The current CI suite runs the following tests:

* Easy Coding Standard

  ```bash
  vendor/bin/ecs
  ```

* PHPStan

  ```bash
  vendor/bin/phpstan 
  ```

* PHPUnit

  ```bash
  vendor/bin/phpunit
  ```

* Behat

  ```bash
  vendor/bin/behat --strict -vvv --no-interaction
  ```

By default the Behat tests is using a fake client.<br />
You can test the scenarios with your own API key make sure to set your API Key in file `tests/Application/.env.local`.<br />
Than adjust the Client declaration in `tests/Behat/Resources/services.yml` like this :
```yaml
services:
    klehm_sylius_brevo_plugin.api.brevo_client:
        class: 'Klehm\SyliusBrevoPlugin\Api\BrevoApiClient'
        public: true
        arguments:
            $apiKey: "%klehm_sylius_brevo.api_key%"
            $templateMessagePayloadBuilder: '@Klehm\SyliusBrevoPlugin\Api\Builder\TemplateMessagePayloadBuilderInterface'
            $htmlMessagePayloadBuilder: '@Klehm\SyliusBrevoPlugin\Api\Builder\HtmlMessagePayloadBuilderInterface'

    # Remove this declaration
    # Klehm\SyliusBrevoPlugin\Cli\ListBrevoTransactionalTemplatesCommand:
    #     arguments:
    #         $brevoApiClient: '@klehm_sylius_brevo_plugin.api.brevo_client'
    #     tags:
    #         - { name: "console.command" }
```
