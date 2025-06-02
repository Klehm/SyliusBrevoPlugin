<?php

declare(strict_types=1);

namespace Tests\Klehm\SyliusBrevoPlugin\Behat\Context\Cli;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

final class ListBrevoTransactionalTemplatesContext implements Context
{
    private const LIST_BREVO_TRANSACTIONAL_TEMPLATES_COMMAND = 'brevo:transactional-templates:list';

    private Application $application;

    private ?CommandTester $commandTester = null;

    public function __construct(
        KernelInterface $kernel,
    ) {
        $this->application = new Application($kernel);
    }

    #[When('I run the Brevo templates list command')]
    public function iRunTheBrevoTemplatesListCommand(): void
    {
        $command = $this->application->find(self::LIST_BREVO_TRANSACTIONAL_TEMPLATES_COMMAND);

        $this->commandTester = new CommandTester($command);
        $this->commandTester->execute(['command' => self::LIST_BREVO_TRANSACTIONAL_TEMPLATES_COMMAND]);
    }

    #[Then('the response should be successful')]
    public function theResponseShouldBeSuccessful(): void
    {
        $this->commandTester?->assertCommandIsSuccessful();
    }
}
