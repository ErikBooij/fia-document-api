<?php
declare(strict_types=1);

use FIADocumentAPI\Application\DocumentRepositoryInterface;

include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/push-notification.function.php';

$container = include __DIR__ . '/../container.php';

$previouslySentFile = __DIR__ . '/../cache/previous';
$previouslySent = [];

if (file_exists($previouslySentFile) && is_readable($previouslySentFile)) {
    $previouslySentFileContents = file_get_contents($previouslySentFile);
    $parsed = json_decode($previouslySentFileContents, true);

    if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
        $previouslySent = $parsed['data'];
    }
}

/** @var DocumentRepositoryInterface $documentRepository */
$documentRepository = $container->get(DocumentRepositoryInterface::class);

$documents = $documentRepository->getAll();

foreach ($documents as $documentGroup) {
    foreach ($documentGroup->getDocuments() as $document) {
        if (!in_array($document->getUrl(), $previouslySent)) {
            echo 'Sending notification for: ', $document->getUrl(), PHP_EOL;

            sendPushNotification($document->getUrl(), $document->getTitle());

            $previouslySent[] = $document->getUrl();
        }
    }
}

file_put_contents(
    $previouslySentFile,
    json_encode([
        'data' => $previouslySent,
        'saved' => (new DateTimeImmutable)->format(DateTime::ATOM)
    ], JSON_PRETTY_PRINT)
);
