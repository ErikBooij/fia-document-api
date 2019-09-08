<?php
declare(strict_types=1);

use FIADocumentAPI\Application\DocumentRepositoryInterface;
use FIADocumentAPI\Application\UnableToFetchDocumentsException;
use FIADocumentAPI\Infrastructure\FileSystemCache;
use FIADocumentAPI\Infrastructure\HTMLResponseParserInterface;
use FIADocumentAPI\Infrastructure\HttpDocumentRepository;
use FIADocumentAPI\Infrastructure\ResponseParserXML;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use League\Container\Container;

include __DIR__ . '/../vendor/autoload.php';

$container = include __DIR__ . '/../container.php';

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
