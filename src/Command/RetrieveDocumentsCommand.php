<?php

namespace App\Command;

use App\Service\ApplicationDocumentService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RetrieveDocumentsCommand extends Command
{
    protected static $defaultName = 'app:retrieve-documents';

    private ApplicationDocumentService $documentService;

    public function __construct(ApplicationDocumentService $documentService)
    {
        parent::__construct();
        $this->documentService = $documentService;
    }

 protected function configure(): void
    {
         $this->setName('app:retrieve-documents') // Explicitly set the name
         ->setDescription('Retrieves documents from a remote source.')
         ->setHelp('This command allows you to retrieve documents...');
    }
	
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Fetching and saving documents...');
        $results = $this->documentService->fetchAndStoreDocuments();

        foreach ($results as $result) {
            $output->writeln($result);
        }

        return Command::SUCCESS;
    }
}
