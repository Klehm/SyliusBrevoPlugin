<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoPlugin\Api;

use Klehm\SyliusBrevoPlugin\Api\Builder\HtmlMessagePayloadBuilderInterface;
use Klehm\SyliusBrevoPlugin\Api\Builder\TemplateMessagePayloadBuilderInterface;
use Klehm\SyliusBrevoPlugin\Model\HtmlMessage;
use Klehm\SyliusBrevoPlugin\Model\TemplateMessage;

class BrevoApiClient implements BrevoApiClientInterface
{
    private string $baseUrl = 'https://api.brevo.com/v3/';

    public function __construct(
        private string $apiKey,
        private TemplateMessagePayloadBuilderInterface $templateMessagePayloadBuilder,
        private HtmlMessagePayloadBuilderInterface $htmlMessagePayloadBuilder,
    ) {
    }

    public function sendEmailWithTemplate(TemplateMessage $message): ?string
    {
        $result = $this->post('smtp/email', $this->templateMessagePayloadBuilder->build($message));

        return $result['messageId'] ?? null;
    }

    public function sendHtmlEmail(HtmlMessage $message): ?string
    {
        $result = $this->post('smtp/email', $this->htmlMessagePayloadBuilder->build($message));

        return $result['messageId'] ?? null;
    }

    public function getTemplates(): array
    {
        $results = $this->get('smtp/templates', [
            'limit' => 1000,
            'offset' => 0,
        ]);

        $templates = $results['templates'] ?? [];

        return $templates;
    }

    public function getTransactionalEmailsByEmail(string $email): array
    {
        $results = $this->get('smtp/emails', [
            'email' => $email,
            'sort' => 'desc',
            'limit' => 10,
        ]);

        return $results['transactionalEmails'] ?? [];
    }

    public function get(string $endpoint, array $params = []): array
    {
        $url = $this->baseUrl . $endpoint;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init();

        $headers = [
            'Accept: application/json',
            'api-key: ' . $this->apiKey,
        ];

        curl_setopt_array($ch, [
            \CURLOPT_URL => $url,
            \CURLOPT_RETURNTRANSFER => true,
            \CURLOPT_HTTPHEADER => $headers,
            \CURLOPT_FOLLOWLOCATION => true,
            \CURLOPT_MAXREDIRS => 1,
        ]);

        /** @var string|false $response The response returned from the cURL execution. */
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, \CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new \RuntimeException("cURL error while requesting Brevo API: $error");
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \RuntimeException("Brevo API request failed with status $httpCode for endpoint '$endpoint'. Response: " . $response);
        }

        // @phpstan-ignore-next-line
        $data = json_decode($response, true);

        if (json_last_error() !== \JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to decode Brevo API response: ' . json_last_error_msg());
        }

        return is_array($data) ? $data : [];
    }

    public function post(string $endpoint, array $data): array
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init();

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'api-key: ' . $this->apiKey,
        ];

        curl_setopt_array($ch, [
            \CURLOPT_URL => $url,
            \CURLOPT_RETURNTRANSFER => true,
            \CURLOPT_HTTPHEADER => $headers,
            \CURLOPT_POST => true,
            \CURLOPT_POSTFIELDS => json_encode($data),
            \CURLOPT_FOLLOWLOCATION => true,
            \CURLOPT_MAXREDIRS => 1,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, \CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new \RuntimeException("cURL error while requesting Brevo API: $error");
        }

        curl_close($ch);

        if (!in_array($httpCode, [200, 201, 202, 204])) {
            throw new \RuntimeException(
                "Brevo API request failed with status $httpCode for endpoint '$endpoint'. Response: " . $response . "\nData: " . json_encode($data, \JSON_PRETTY_PRINT),
            );
        }

        $data = json_decode($response, true);

        if (json_last_error() !== \JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to decode Brevo API response: ' . json_last_error_msg());
        }

        return is_array($data) ? $data : [];
    }
}
