<?php

declare(strict_types=1);

namespace Tests\Klehm\SyliusBrevoPlugin\Unit\DependencyInjection;

use Klehm\SyliusBrevoPlugin\DependencyInjection\KlehmSyliusBrevoExtension;
use Klehm\SyliusBrevoPlugin\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


final class KlehmSyliusBrevoExtensionTest extends TestCase
{
    public function testLoadSetsParametersFromConfig(): void
    {
        $extension = new KlehmSyliusBrevoExtension();
        $container = new ContainerBuilder();

        $configs = [[
            'api_key' => 'test-key',
            'templates' => [
                'en_US' => [
                    'order_confirmation' => 123,
                    'password_reset' => 456,
                ],
            ],
        ]];

        // Mock YamlFileLoader to avoid loading actual files
        $loaderMock = $this->getMockBuilder(YamlFileLoader::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Use Reflection to inject the mock loader if needed, or just call load (it will fail gracefully if file not found)
        try {
            $extension->load($configs, $container);
        } catch (\Exception $e) {
            // Ignore file not found, we only care about parameters
        }

        $this->assertTrue($container->hasParameter('klehm_sylius_brevo.api_key'));
        $this->assertTrue($container->hasParameter('klehm_sylius_brevo.templates'));

        $this->assertSame('test-key', $container->getParameter('klehm_sylius_brevo.api_key'));
        $this->assertSame([
            'en_US' => [
                'order_confirmation' => 123,
                'password_reset' => 456,
            ]
        ], $container->getParameter('klehm_sylius_brevo.templates'));
    }

    public function testLoadSetsDefaultParametersIfNotProvided(): void
    {
        $extension = new KlehmSyliusBrevoExtension();
        $container = new ContainerBuilder();

        $configs = [[]];

        try {
            $extension->load($configs, $container);
        } catch (\Exception $e) {
            // Ignore file not found
        }

        $this->assertTrue($container->hasParameter('klehm_sylius_brevo.api_key'));
        $this->assertTrue($container->hasParameter('klehm_sylius_brevo.templates'));

        $this->assertSame('', $container->getParameter('klehm_sylius_brevo.api_key'));
        $this->assertSame([], $container->getParameter('klehm_sylius_brevo.templates'));
    }
}
