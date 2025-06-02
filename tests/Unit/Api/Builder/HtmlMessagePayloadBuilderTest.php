<?php

declare(strict_types=1);

namespace Tests\Klehm\SyliusBrevoPlugin\Api\Builder;

use Klehm\SyliusBrevoPlugin\Api\Builder\HtmlMessagePayloadBuilder;
use Klehm\SyliusBrevoPlugin\Model\HtmlMessage;
use PHPUnit\Framework\TestCase;

final class HtmlMessagePayloadBuilderTest extends TestCase
{
    public function testBuildReturnsExpectedPayload(): void
    {
        $message = $this->createMock(HtmlMessage::class);

        $message->method('getFrom')->willReturn(['name' => 'Sender', 'email' => 'sender@example.com']);
        $message->method('getTo')->willReturn([['email' => 'to@example.com']]);
        $message->method('getReplyTo')->willReturn([['email' => 'reply@example.com']]);
        $message->method('getCc')->willReturn([['email' => 'cc@example.com']]);
        $message->method('getBcc')->willReturn([['email' => 'bcc@example.com']]);
        $message->method('getData')->willReturn(['foo' => 'bar']);
        $message->method('getAttachments')->willReturn([['url' => 'http://example.com/file.pdf']]);
        $message->method('getSubject')->willReturn('Test Subject');
        $message->method('getHtmlContent')->willReturn('<p>Hello</p>');

        $builder = new HtmlMessagePayloadBuilder();
        $payload = $builder->build($message);

        $expected = [
            'sender' => ['name' => 'Sender', 'email' => 'sender@example.com'],
            'to' => [['email' => 'to@example.com']],
            'replyTo' => [['email' => 'reply@example.com']],
            'cc' => [['email' => 'cc@example.com']],
            'bcc' => [['email' => 'bcc@example.com']],
            'params' => ['foo' => 'bar'],
            'attachment' => [['url' => 'http://example.com/file.pdf']],
            'subject' => 'Test Subject',
            'htmlContent' => '<p>Hello</p>',
        ];

        $this->assertSame($expected, $payload);
    }

    public function testBuildFiltersNullAndEmptyValues(): void
    {
        $message = $this->createMock(HtmlMessage::class);

        $message->method('getFrom')->willReturn(null);
        $message->method('getTo')->willReturn([]);
        $message->method('getReplyTo')->willReturn(null);
        $message->method('getCc')->willReturn([]);
        $message->method('getBcc')->willReturn(null);
        $message->method('getData')->willReturn([]);
        $message->method('getAttachments')->willReturn([]);
        $message->method('getSubject')->willReturn('Subject');
        $message->method('getHtmlContent')->willReturn('<b>Body</b>');

        $builder = new HtmlMessagePayloadBuilder();
        $payload = $builder->build($message);

        $expected = [
            'subject' => 'Subject',
            'htmlContent' => '<b>Body</b>',
        ];

        $this->assertSame($expected, $payload);
    }
}
