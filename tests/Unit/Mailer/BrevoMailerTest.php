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
use Sylius\Component\Mailer\Event\EmailSendEvent;
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
            ->with($this->isInstanceOf(TemplateMessage::class));

        $dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->with($this->isInstanceOf(EmailSendEvent::class));

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
            ->with($this->isInstanceOf(HtmlMessage::class));

        $dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->with($this->isInstanceOf(EmailSendEvent::class));

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
}
