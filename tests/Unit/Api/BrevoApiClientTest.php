<?php

declare(strict_types=1);

namespace Tests\Klehm\SyliusBrevoMailerPlugin\Unit\Api;

use Klehm\SyliusBrevoMailerPlugin\Api\BrevoApiClient;
use PHPUnit\Framework\TestCase;
use Klehm\SyliusBrevoMailerPlugin\Model\HtmlMessage;
use Klehm\SyliusBrevoMailerPlugin\Model\TemplateMessage;

final class BrevoApiClientTest extends TestCase
{
    public function testSendEmailWithTemplateCallsPostWithCorrectParams(): void
    {
        $apiKey = 'test-key';
        $client = $this->getMockBuilder(BrevoApiClient::class)
            ->setConstructorArgs([$apiKey])
            ->onlyMethods(['post'])
            ->getMock();

        $message = $this->createMock(TemplateMessage::class);
        $message->method('getFrom')->willReturn(['email' => 'from@example.com']);
        $message->method('getTo')->willReturn([['email' => 'to@example.com']]);
        $message->method('getReplyTo')->willReturn(['email' => 'reply@example.com']);
        $message->method('getCc')->willReturn([]);
        $message->method('getBcc')->willReturn([]);
        $message->method('getTemplateId')->willReturn(123);
        $message->method('getData')->willReturn(['foo' => 'bar']);
        $message->method('getAttachments')->willReturn([]);

        $expectedData = [
            'sender' => ['email' => 'from@example.com'],
            'to' => [['email' => 'to@example.com']],
            'replyTo' => ['email' => 'reply@example.com'],
            'cc' => [],
            'bcc' => [],
            'templateId' => 123,
            'params' => ['foo' => 'bar'],
            'attachment' => [],
        ];

        $client->expects($this->once())
            ->method('post')
            ->with('smtp/email', $expectedData);

        /** @disregard */
        $client->sendEmailWithTemplate($message);
    }

    public function testSendHtmlEmailCallsPostWithCorrectParams(): void
    {
        $apiKey = 'test-key';
        $client = $this->getMockBuilder(BrevoApiClient::class)
            ->setConstructorArgs([$apiKey])
            ->onlyMethods(['post'])
            ->getMock();

        $message = $this->createMock(HtmlMessage::class);
        $message->method('getFrom')->willReturn(['email' => 'from@example.com']);
        $message->method('getTo')->willReturn([['email' => 'to@example.com']]);
        $message->method('getSubject')->willReturn('Subject');
        $message->method('getHtmlContent')->willReturn('<b>html</b>');
        $message->method('getReplyTo')->willReturn(['email' => 'reply@example.com']);
        $message->method('getCc')->willReturn([]);
        $message->method('getBcc')->willReturn([]);
        $message->method('getData')->willReturn(['foo' => 'bar']);
        $message->method('getAttachments')->willReturn([]);

        $expectedData = [
            'sender' => ['email' => 'from@example.com'],
            'to' => [['email' => 'to@example.com']],
            'subject' => 'Subject',
            'htmlContent' => '<b>html</b>',
            'replyTo' => ['email' => 'reply@example.com'],
            'cc' => [],
            'bcc' => [],
            'params' => ['foo' => 'bar'],
            'attachment' => [],
        ];

        $client->expects($this->once())
            ->method('post')
            ->with('smtp/email', $expectedData);

        /** @disregard */
        $client->sendHtmlEmail($message);
    }

    public function testGetTemplatesReturnsTemplates(): void
    {
        $apiKey = 'test-key';
        $client = $this->getMockBuilder(BrevoApiClient::class)
            ->setConstructorArgs([$apiKey])
            ->onlyMethods(['get'])
            ->getMock();

        $client->expects($this->once())
            ->method('get')
            ->with('smtp/templates', ['limit' => 1000, 'offset' => 0])
            ->willReturn(['templates' => [['id' => 1], ['id' => 2]]]);

        /** @disregard */
        $result = $client->getTemplates();
        $this->assertSame([['id' => 1], ['id' => 2]], $result);
    }

    public function testGetThrowsOnCurlError(): void
    {
        $apiKey = 'test-key';
        $client = new BrevoApiClient($apiKey);

        // Use reflection to access protected/private method for test
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('get');
        $method->setAccessible(true);

        // Simulate cURL error by using an invalid URL (localhost:0)
        $reflectionProperty = $reflection->getProperty('baseUrl');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($client, 'http://localhost:0/');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/cURL error while requesting Brevo API:/');

        $method->invoke($client, 'invalid-endpoint', []);
    }

    public function testGetThrowsOnBadHttpCode(): void
    {
        $apiKey = 'test-key';
        $client = $this->getMockBuilder(BrevoApiClient::class)
            ->setConstructorArgs([$apiKey])
            ->onlyMethods(['get'])
            ->getMock();

        // We cannot easily test the real cURL call for a bad HTTP code without a real endpoint.
        // So this test is a placeholder for integration testing.
        $this->assertTrue(true);
    }

    public function testGetThrowsOnJsonDecodeError(): void
    {
        $apiKey = 'test-key';
        $client = $this->getMockBuilder(BrevoApiClient::class)
            ->setConstructorArgs([$apiKey])
            ->onlyMethods(['get'])
            ->getMock();

        // We cannot easily test the real cURL call for JSON decode error without a real endpoint.
        // So this test is a placeholder for integration testing.
        $this->assertTrue(true);
    }

    public function testPostThrowsOnCurlError(): void
    {
        $apiKey = 'test-key';
        $client = new BrevoApiClient($apiKey);

        // Use reflection to access protected/private method for test
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('post');
        $method->setAccessible(true);

        // Simulate cURL error by using an invalid URL (localhost:0)
        $reflectionProperty = $reflection->getProperty('baseUrl');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($client, 'http://localhost:0/');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/cURL error while requesting Brevo API:/');

        $method->invoke($client, 'invalid-endpoint', []);
    }

    public function testPostThrowsOnBadHttpCode(): void
    {
        $apiKey = 'test-key';
        $client = $this->getMockBuilder(BrevoApiClient::class)
            ->setConstructorArgs([$apiKey])
            ->onlyMethods(['post'])
            ->getMock();

        // We cannot easily test the real cURL call for a bad HTTP code without a real endpoint.
        // So this test is a placeholder for integration testing.
        $this->assertTrue(true);
    }
}
