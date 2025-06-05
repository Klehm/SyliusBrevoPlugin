<?php

declare(strict_types=1);

namespace Tests\Klehm\SyliusBrevoMailerPlugin\Unit\Provider;

use Klehm\SyliusBrevoMailerPlugin\Provider\TemplateIdProvider;
use PHPUnit\Framework\TestCase;

final class TemplateIdProviderTest extends TestCase
{
    public function testReturnsTemplateIdIfExists(): void
    {
        $provider = new TemplateIdProvider([
            'order_confirmation' => 123,
            'password_reset' => 456,
        ]);

        $this->assertSame(123, $provider->getId('order_confirmation'));
        $this->assertSame(456, $provider->getId('password_reset'));
    }

    public function testReturnsNullIfTemplateDoesNotExist(): void
    {
        $provider = new TemplateIdProvider([
            'order_confirmation' => 123,
        ]);

        $this->assertNull($provider->getId('non_existing_template'));
    }
}
