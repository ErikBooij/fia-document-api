<?php
declare(strict_types=1);

$options = getopt('', ['document-title:', 'document-url:']);

if (!isset($options['document-title'], $options['document-url'])) {
    exit;
}

$documentTitle = trim($options['document-title']);
$documentUrl = trim($options['document-url']);

include __DIR__ . '/vendor/autoload.php';

use Apple\ApnPush\Jwt\Jwt;
use Apple\ApnPush\Model\DeviceToken;
use Apple\ApnPush\Model\Notification;
use Apple\ApnPush\Model\Receiver;
use Apple\ApnPush\Protocol\Http\Authenticator\JwtAuthenticator;
use Apple\ApnPush\Sender\Builder\Http20Builder;
use Apple\ApnPush\Sender\Sender;

$creds = json_decode(file_get_contents(__DIR__ . '/config.json'), true);

$key = $creds['key'] ?? '';

$jwt = new Jwt($creds['teamId'] ?? '', $key ?? '', __DIR__ . "/AuthKey_{$key}.p8");
$authenticator = new JwtAuthenticator($jwt);

$builder = new Http20Builder($authenticator);

$protocol = $builder->buildProtocol();
$sender = new Sender($protocol);

$notification = Notification::createWithBody("The FIA released a new document: '{$documentTitle}'");

$notification = $notification->withPayload(
    $notification->getPayload()
                 ->withCustomData('title', $documentTitle)
                 ->withCustomData('url', $documentUrl)
);

$receiver = new Receiver(
    new DeviceToken($creds['deviceToken'] ?? ''),
    'me.Booij.F1-Docs'
);

$sender->send($receiver, $notification, true);

$protocol->closeConnection();
