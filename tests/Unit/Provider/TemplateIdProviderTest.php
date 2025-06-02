<?php

declare(strict_types=1);

namespace Tests\Klehm\SyliusBrevoPlugin\Unit\Provider;

use Klehm\SyliusBrevoPlugin\Provider\TemplateIdProvider;
use PHPUnit\Framework\TestCase;

final class TemplateIdProviderTest extends TestCase
{
    public function testReturnsTemplateIdIfExists(): void
    {
        $provider = new TemplateIdProvider([
            'en_US' => [
                'order_confirmation' => 123,
                'password_reset' => 456,
            ],
        ]);

        $this->assertSame(123, $provider->getId('order_confirmation', 'en_US'));
        $this->assertSame(456, $provider->getId('password_reset', 'en_US'));
    }

    public function testThrowsExceptionIfTemplateIdIsNotInteger(): void
    {
        $provider = new TemplateIdProvider([
            'en_US' => [
                'order_confirmation' => 'not_an_integer',
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Template ID for "order_confirmation" in locale "en_US" must be an integer.');
        $provider->getId('order_confirmation', 'en_US');
    }

    public function testReturnsNullIfTemplateDoesNotExist(): void
    {
        $provider = new TemplateIdProvider([
            'en_US' => [
                'order_confirmation' => 123,
            ],
        ]);

        $this->assertNull($provider->getId('non_existing_template', 'en_US'));
    }
}
