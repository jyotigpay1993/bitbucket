<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class ApplicationDocumentService
{
    private HttpClientInterface $httpClient;
    private string $DocumentPath;
    private LoggerInterface $logger;
	const URL = 'https://raw.githubusercontent.com/RashitKhamidullin/Educhain-Assignment/refs/heads/main/get-documents';
	const MANDATORY_FIELDS = ['certificate', 'description', 'doc_no'];


    public function __construct(HttpClientInterface $httpClient, string $DocumentPath, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->DocumentPath = rtrim($DocumentPath, '/'); // Ensure no trailing slash
        $this->logger = $logger;
    }

    //Function - Fetch and store document from API Call
    public function fetchAndStoreDocuments(): array
    {
        $documentsList = array();

        try {
            $response = $this->httpClient->request('GET', self::URL);
            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                $this->logger->error("Error in fetching the documents from API call , status: $statusCode");
                throw new \Exception("Failed to fetch data from API.");
            }

            $documents = $response->toArray();

            foreach ($documents as $document) {
                $documentsList[] = $this->saveDocument($document);
            }
        } catch (\Exception $e) {
            $this->logger->error('Error occurred while fetching or processing documents: ' . $e->getMessage());
            return ['error' => 'An error occurred while processing documents.'];
        }

        return $documentsList;
    }

    //Function - Decode and save each document
    private function saveDocument(array $document): string
    {
        try {		
		   foreach (self::MANDATORY_FIELDS as $mandatory_field_index) {
				if (!isset($document[$mandatory_field_index]) || empty($document[$mandatory_field_index])) {
					throw new \InvalidArgumentException('Document data is invalid , missing the required fields.');
				}
			}

            // considering the ceritificate is base64-encode , so decoding it
            $decodedFile = base64_decode($document['certificate']);
            if ($decodedFile === false) {
                throw new \Exception('Failed to decode base64 content of certificate data.');
            }

            $file_description = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $document['description']);
            $fileName = $file_description . '_' . $document['doc_no'] . '.pdf';
            $filePath = $this->DocumentPath . '/' . $fileName;

            file_put_contents($filePath, $decodedFile);

            $this->logger->info("Document saved successfully: $filePath");
            return "Document saved: $fileName";
        } catch (\Exception $e) {
            $this->logger->error("Error processing document: " . $e->getMessage());
            return "Error processing document: " . $e->getMessage();
        }
    }

}
