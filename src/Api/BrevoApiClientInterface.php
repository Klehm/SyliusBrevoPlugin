<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoPlugin\Api;

use Klehm\SyliusBrevoPlugin\Model\HtmlMessage;
use Klehm\SyliusBrevoPlugin\Model\TemplateMessage;

interface BrevoApiClientInterface
{
    /**
     * Sends an email using a template ID via the Brevo API.
     * Reference: https://developers.brevo.com/reference/smtp-email
     *
     * @param TemplateMessage $message The message containing email details and template ID.
     *
     * @return string|null The Message ID of the sent email, or null if the request fails.
     *
     * @throws \RuntimeException If the request fails or returns an unexpected status code.
     */
    public function sendEmailWithTemplate(TemplateMessage $message): ?string;

    /**
     * Sends an HTML email using the Brevo API.
     * Reference: https://developers.brevo.com/reference/smtp-email
     *
     * @param HtmlMessage $message The message containing email details.
     *
     * @return string|null The Message ID of the sent email, or null if the request fails.
     *
     * @throws \RuntimeException If the request fails or returns an unexpected status code.
     */
    public function sendHtmlEmail(HtmlMessage $message): ?string;

    /**
     * Retrieves a list of email templates from the Brevo API.
     * Reference: https://developers.brevo.com/reference/gettemplates
     *
     * @return array An array of templates, each containing 'id', 'name', and 'subject'.
     */
    public function getTemplates(): array;

    /**
     * Retrieves a list of transactional email templates from the Brevo API.
     * Reference: https://developers.brevo.com/reference/gettransacemailslist
     *
     * @param string $email The email address to filter transactional emails by.
     *
     * @return array An array of transactional email templates, each containing 'email', 'templateId', 'subject' and 'date'.
     */
    public function getTransactionalEmailsByEmail(string $email): array;

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
     * @return array The decoded JSON response from the API.
     *
     * @throws \RuntimeException If the request fails or returns an unexpected status code.
     */
    public function post(string $endpoint, array $data): array;
}
