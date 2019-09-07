<?php
declare(strict_types=1);

use FIADocumentAPI\Application\DocumentRepositoryInterface;
use FIADocumentAPI\Application\UnableToFetchDocumentsException;
use FIADocumentAPI\Infrastructure\HTMLResponseParserInterface;
use FIADocumentAPI\Infrastructure\HttpDocumentRepository;
use FIADocumentAPI\Infrastructure\ResponseParserXML;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use League\Container\Container;

include __DIR__ . '/../vendor/autoload.php';

$container = new Container;
$container->add(DocumentRepositoryInterface::class, HttpDocumentRepository::class)
    ->addArguments([
        ClientInterface::class,
        HTMLResponseParserInterface::class,
    ]);
$container->add(HTMLResponseParserInterface::class, ResponseParserXML::class);
$container->add(ClientInterface::class, Client::class);

/** @var DocumentRepositoryInterface $repository */
$repository = $container->get(DocumentRepositoryInterface::class);

$statusCode = 200;
$status = 'success';
$data = [];

try {
    $data = $repository->getAll();
} catch (UnableToFetchDocumentsException $exception) {
    $statusCode = 500;
    $status = 'failure';

    $data = [];

    do {
        $data[] = $exception->getMessage();
    } while ($exception = $exception->getPrevious());
}

header('Content-Type: application/json', true, $statusCode);
echo json_encode([
    'status' => $status,
    'data' => $data,
], JSON_PRETTY_PRINT);
