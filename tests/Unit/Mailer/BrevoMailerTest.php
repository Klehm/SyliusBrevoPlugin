<?php

declare(strict_types=1);

namespace Tests\Klehm\SyliusBrevoPlugin\Unit\Mailer;

use Klehm\SyliusBrevoPlugin\Api\BrevoApiClientInterface;
use Klehm\SyliusBrevoPlugin\Mailer\BrevoMailer;
use Klehm\SyliusBrevoPlugin\Model\HtmlMessage;
use Klehm\SyliusBrevoPlugin\Model\TemplateMessage;
use Klehm\SyliusBrevoPlugin\Provider\TemplateIdProviderInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class BrevoMailerTest extends TestCase
{
    public function testSendWithTemplateId(): void
    {
        $templateIdProvider = $this->createMock(TemplateIdProviderInterface::class);
        $apiClient = $this->createMock(BrevoApiClientInterface::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $localeContext = $this->createMock(LocaleContextInterface::class);

        $templateIdProvider->method('getId')->willReturn(123);

        $apiClient->expects($this->once())
            ->method('sendEmailWithTemplate')
            ->with($this->callback(function ($message) use (&$calledMessage) {
                $calledMessage = $message;
                return $message instanceof TemplateMessage;
            }));


        $dispatcher->expects($this->atLeast(3))
            ->method('dispatch');

        $mailer = new BrevoMailer($templateIdProvider, $apiClient, $dispatcher, $localeContext);
        $localeContext->method('getLocaleCode')->willReturn('en_US');

        $recipients = ['john@example.com', 'jane@example.com'];
        $senderAddress = 'shop@example.com';
        $senderName = 'Shop';
        $renderedEmail = $this->createMock(RenderedEmail::class);
        $email = $this->createMock(EmailInterface::class);
        $email->method('getCode')->willReturn('order_confirmation');
        $data = ['foo' => 'bar'];

        $mailer->send($recipients, $senderAddress, $senderName, $renderedEmail, $email, $data);

        // Assertions
        $this->assertInstanceOf(TemplateMessage::class, $calledMessage);
        $this->assertSame(123, $calledMessage->getTemplateId());
        $this->assertSame($senderAddress, $calledMessage->getFrom()['email']);
        $this->assertSame($senderName, $calledMessage->getFrom()['name']);
        $this->assertIsArray($calledMessage->getTo());
        $this->assertCount(2, $calledMessage->getTo());
        $this->assertArrayHasKey('foo', $calledMessage->getData());
        $this->assertSame('bar', $calledMessage->getData()['foo']);
    }

    public function testSendWithoutTemplateId(): void
    {
        $templateIdProvider = $this->createMock(TemplateIdProviderInterface::class);
        $apiClient = $this->createMock(BrevoApiClientInterface::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $localeContext = $this->createMock(LocaleContextInterface::class);

        $templateIdProvider->method('getId')->willReturn(null);

        $apiClient->expects($this->once())
            ->method('sendHtmlEmail')
            ->with($this->callback(function ($message) use (&$calledMessage) {
            $calledMessage = $message;
            return $message instanceof HtmlMessage;
            }));

        $dispatcher->expects($this->atLeast(3))
            ->method('dispatch');

        $mailer = new BrevoMailer($templateIdProvider, $apiClient, $dispatcher, $localeContext);
        $localeContext->method('getLocaleCode')->willReturn('en_US');

        $recipients = ['john@example.com', 'jane@example.com'];
        $senderAddress = 'shop@example.com';
        $senderName = 'Shop';
        $renderedEmail = $this->createMock(RenderedEmail::class);
        $renderedEmail->method('getSubject')->willReturn('Subject');
        $renderedEmail->method('getBody')->willReturn('<p>Body</p>');
        $email = $this->createMock(EmailInterface::class);
        $email->method('getCode')->willReturn('unknown_template');
        $data = ['foo' => 'bar'];

        $mailer->send($recipients, $senderAddress, $senderName, $renderedEmail, $email, $data);

        // Assertions
        $this->assertInstanceOf(HtmlMessage::class, $calledMessage);
        $this->assertSame('Subject', $calledMessage->getSubject());
        $this->assertSame('<p>Body</p>', $calledMessage->getHtmlContent());
        $this->assertSame($senderAddress, $calledMessage->getFrom()['email']);
        $this->assertSame($senderName, $calledMessage->getFrom()['name']);
        $this->assertIsArray($calledMessage->getTo());
        $this->assertCount(2, $calledMessage->getTo());
        $this->assertArrayHasKey('foo', $calledMessage->getData());
        $this->assertSame('bar', $calledMessage->getData()['foo']);
    }

    public function testFormatRecipients(): void
    {
        $mailer = new BrevoMailer(
            $this->createMock(TemplateIdProviderInterface::class),
            $this->createMock(BrevoApiClientInterface::class),
            $this->createMock(EventDispatcherInterface::class),
            $this->createMock(LocaleContextInterface::class)
        );

        $recipients = [
            'john@example.com' => 'John Doe',
            'jane@example.com' => 'Jane Smith',
            'foo@example.com',
        ];

        $result = $this->invokeProtected($mailer, 'formatRecipients', [$recipients]);

        $this->assertContains(['name' => 'John Doe', 'email' => 'john@example.com'], $result);
        $this->assertContains(['name' => 'Jane Smith', 'email' => 'jane@example.com'], $result);
        $this->assertContains(['email' => 'foo@example.com'], $result);
    }

    public function testFormatAttachmentsWithFilePath(): void
    {
        $mailer = new BrevoMailer(
            $this->createMock(TemplateIdProviderInterface::class),
            $this->createMock(BrevoApiClientInterface::class),
            $this->createMock(EventDispatcherInterface::class),
            $this->createMock(LocaleContextInterface::class)
        );

        $tmpFile = tempnam(sys_get_temp_dir(), 'att');
        file_put_contents($tmpFile, 'test content');

        $attachments = [
            [
                'filePath' => $tmpFile,
                'fileName' => 'test.txt',
            ],
        ];

        $result = $this->invokeProtected($mailer, 'formatAttachments', [$attachments]);

        $this->assertCount(1, $result);
        $this->assertSame('test.txt', $result[0]['name']);
        $this->assertSame(base64_encode('test content'), $result[0]['content']);

        unlink($tmpFile);
    }

    public function testFormatAttachmentsWithContent(): void
    {
        $mailer = new BrevoMailer(
            $this->createMock(TemplateIdProviderInterface::class),
            $this->createMock(BrevoApiClientInterface::class),
            $this->createMock(EventDispatcherInterface::class),
            $this->createMock(LocaleContextInterface::class)
        );

        $attachments = [
            [
                'content' => 'raw content',
                'fileName' => 'inline.txt',
            ],
        ];

        $result = $this->invokeProtected($mailer, 'formatAttachments', [$attachments]);

        $this->assertCount(1, $result);
        $this->assertSame('inline.txt', $result[0]['name']);
        $this->assertSame(base64_encode('raw content'), $result[0]['content']);
    }

    private function invokeProtected(object $object, string $method, array $args)
    {
        $ref = new \ReflectionClass($object);
        $m = $ref->getMethod($method);
        $m->setAccessible(true);
        return $m->invokeArgs($object, $args);
    }

    public function testFormatRecipientsWithInvalidEmailFallsBackToEmailOnly(): void
    {
        $mailer = new \Klehm\SyliusBrevoPlugin\Mailer\BrevoMailer(
            $this->createMock(\Klehm\SyliusBrevoPlugin\Provider\TemplateIdProviderInterface::class),
            $this->createMock(\Klehm\SyliusBrevoPlugin\Api\BrevoApiClientInterface::class),
            $this->createMock(\Symfony\Contracts\EventDispatcher\EventDispatcherInterface::class),
            $this->createMock(\Sylius\Component\Locale\Context\LocaleContextInterface::class)
        );

        $recipients = [
            'not-an-email' => 'Invalid Name',
            'valid@example.com' => 'Valid Name',
            'foo@example.com',
        ];

        $result = $this->invokeProtected($mailer, 'formatRecipients', [$recipients]);

        $this->assertNotContains(['email' => 'not-an-email'], $result);
        $this->assertContains(['name' => 'Valid Name', 'email' => 'valid@example.com'], $result);
        $this->assertContains(['email' => 'foo@example.com'], $result);
    }

    public function testFormatAttachmentsThrowsOnUnreadableFile(): void
    {
        $mailer = new \Klehm\SyliusBrevoPlugin\Mailer\BrevoMailer(
            $this->createMock(\Klehm\SyliusBrevoPlugin\Provider\TemplateIdProviderInterface::class),
            $this->createMock(\Klehm\SyliusBrevoPlugin\Api\BrevoApiClientInterface::class),
            $this->createMock(\Symfony\Contracts\EventDispatcher\EventDispatcherInterface::class),
            $this->createMock(\Sylius\Component\Locale\Context\LocaleContextInterface::class)
        );

        $attachments = [
            [
                'filePath' => '/path/to/nonexistent/file.txt',
                'fileName' => 'file.txt',
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->invokeProtected($mailer, 'formatAttachments', [$attachments]);
    }

    public function testCollectData(): void
    {
        $mailer = new BrevoMailer(
            $this->createMock(TemplateIdProviderInterface::class),
            $this->createMock(BrevoApiClientInterface::class),
            $this->createMock(EventDispatcherInterface::class),
            $this->createMock(LocaleContextInterface::class)
        );

        $email = $this->createMock(EmailInterface::class);

        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $result = $this->invokeProtected($mailer, 'collectData', [$data, $email]);

        $this->assertArrayHasKey('key1', $result);
        $this->assertArrayHasKey('key2', $result);
    }

    public function testCollectDataWithEmptyData(): void
    {
        $mailer = new BrevoMailer(
            $this->createMock(TemplateIdProviderInterface::class),
            $this->createMock(BrevoApiClientInterface::class),
            $this->createMock(EventDispatcherInterface::class),
            $this->createMock(LocaleContextInterface::class)
        );

        $email = $this->createMock(EmailInterface::class);

        $result = $this->invokeProtected($mailer, 'collectData', [[], $email]);

        $this->assertEmpty($result);
    }

    public function testCollectDataWithIncorretArray(): void
    {
        $mailer = new BrevoMailer(
            $this->createMock(TemplateIdProviderInterface::class),
            $this->createMock(BrevoApiClientInterface::class),
            $this->createMock(EventDispatcherInterface::class),
            $this->createMock(LocaleContextInterface::class)
        );

        $email = $this->createMock(EmailInterface::class);
        $email->method('getCode')->willReturn(null);

        $data = ['key1' => 'value1', 'key2' => ['value1', 'value2']];
        $result = $this->invokeProtected($mailer, 'collectData', [$data, $email]);

        $this->assertArrayHasKey('key1', $result);
        $this->assertArrayNotHasKey('key2', $result);
    }
}
