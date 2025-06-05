<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoMailerPlugin\Api;

use Klehm\SyliusBrevoMailerPlugin\Model\HtmlMessage;
use Klehm\SyliusBrevoMailerPlugin\Model\TemplateMessage;

interface BrevoApiClientInterface
{
    /**
     * Sends an email using a template ID via the Brevo API.
     * Reference: https://developers.brevo.com/reference/smtp-email
     *
     * @param TemplateMessage $message The message containing email details and template ID.
     *
     * @throws \RuntimeException If the request fails or returns an unexpected status code.
     */
    public function sendEmailWithTemplate(TemplateMessage $message): void;

    /**
     * Sends an HTML email using the Brevo API.
     * Reference: https://developers.brevo.com/reference/smtp-email
     *
     * @param HtmlMessage $message The message containing email details.
     *
     * @throws \RuntimeException If the request fails or returns an unexpected status code.
     */
    public function sendHtmlEmail(HtmlMessage $message): void;

    /**
     * Retrieves a list of email templates from the Brevo API.
     * Reference: https://developers.brevo.com/reference/gettemplates
     *
     * @return array An array of templates, each containing 'id', 'name', and 'subject'.
     */
    public function getTemplates(): array;

    /**
     * Sends a GET request to the specified Brevo API endpoint with optional query parameters.
     *
     * @param string $endpoint The API endpoint to send the request to.
     * @param array $params Optional query parameters to include in the request.
     *
     * @return array The decoded JSON response from the API.
     *
     * @throws \RuntimeException If the request fails or returns an unexpected status code.
     */
    public function get(string $endpoint, array $params = []): array;

    /**
     * Sends a POST request to the specified Brevo API endpoint with the given data.
     *
     * @param string $endpoint The API endpoint to send the request to.
     * @param array $data The data to include in the POST request body.
     *
     * @throws \RuntimeException If the request fails or returns an unexpected status code.
     */
    public function post(string $endpoint, array $data): void;
}
