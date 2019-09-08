<?php
declare(strict_types=1);

$options = getopt('', ['document-title:', 'document-url:']);

if (!isset($options['document-title'], $options['document-url'])) {
    exit;
}

$documentTitle = trim($options['document-title']);
$documentUrl = trim($options['document-url']);

include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/push-notification.function.php';

sendPushNotification($documentUrl, $documentTitle);
