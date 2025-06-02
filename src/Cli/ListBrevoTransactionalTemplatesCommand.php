<?php

declare(strict_types=1);

namespace Klehm\SyliusBrevoPlugin\Cli;

use Klehm\SyliusBrevoPlugin\Api\BrevoApiClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'brevo:transactional-templates:list',
    description: 'Lists all Brevo transactional email templates configured in the application.',
)]
class ListBrevoTransactionalTemplatesCommand extends Command
{
    public function __construct(
        private BrevoApiClientInterface $brevoApiClient,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Brevo Transactional Email Templates');

        try {
            $templates = $this->brevoApiClient->getTemplates();
        } catch (\Throwable $th) {
            $io->error('Failed to fetch Brevo transactional email templates: ' . $th->getMessage());

            return Command::FAILURE;
        }

        if (empty($templates)) {
            $io->warning('No Brevo transactional email templates found.');
        } else {
            $io->section('Available Templates');

            $templateList = [];
            foreach ($templates as $template) {
                $templateList[] = [
                    'ID' => $template['id'],
                    'Name' => $template['name'] ?? 'N/A',
                    'Subject' => $template['subject'] ?? 'N/A',
                ];
            }

            $io->table(['ID', 'Name', 'Subject'], $templateList);
        }

        return Command::SUCCESS;
    }
}
