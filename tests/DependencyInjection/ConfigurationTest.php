<?php

declare(strict_types=1);

namespace Tests\Klehm\SyliusBrevoPlugin\Unit\DependencyInjection;

use Klehm\SyliusBrevoPlugin\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;


final class ConfigurationTest extends TestCase
{
    public function testDefaultConfig(): void
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, []);

        $this->assertArrayHasKey('api_key', $config);
        $this->assertSame('', $config['api_key']);
        $this->assertArrayHasKey('templates', $config);
        $this->assertSame([], $config['templates']);
    }

    public function testCustomApiKeyAndTemplates(): void
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $input = [
            'api_key' => 'my-secret-key',
            'templates' => [
                'en_US' => [
                    'order_confirmation' => 123,
                    'password_reset' => 456,
                ]
            ],
        ];

        $config = $processor->processConfiguration($configuration, [$input]);

        $this->assertSame('my-secret-key', $config['api_key']);
        $this->assertSame([
            'en_US' => [
                'order_confirmation' => 123,
                'password_reset' => 456
            ]
        ], $config['templates']);
    }

    public function testApiKeyCannotBeEmpty(): void
    {
        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);

        $processor = new Processor();
        $configuration = new Configuration();

        $input = [
            'klehm_sylius_brevo' => [
                'api_key' => '',
            ],
        ];

        $processor->processConfiguration($configuration, [$input]);
    }
}
