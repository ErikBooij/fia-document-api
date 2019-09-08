<?php
declare(strict_types=1);

use Apple\ApnPush\Jwt\Jwt;
use Apple\ApnPush\Model\DeviceToken;
use Apple\ApnPush\Model\Notification;
use Apple\ApnPush\Model\Receiver;
use Apple\ApnPush\Protocol\Http\Authenticator\JwtAuthenticator;
use Apple\ApnPush\Sender\Builder\Http20Builder;
use Apple\ApnPush\Sender\Sender;

function sendPushNotification(string $url, string $title): void
{
    $creds = json_decode(file_get_contents(__DIR__ . '/../config.json'), true);

    $key = $creds['key'] ?? '';

    $jwt           = new Jwt($creds['teamId'] ?? '', $key ?? '', __DIR__ . "/../AuthKey_{$key}.p8");
    $authenticator = new JwtAuthenticator($jwt);

    $builder = new Http20Builder($authenticator);

    $protocol = $builder->buildProtocol();
    $sender   = new Sender($protocol);

    $notification = Notification::createWithBody("The FIA released a new document: '{$title}'");

    $notification = $notification->withPayload($notification->getPayload()
                                                            ->withCustomData('title', $title)
                                                            ->withCustomData('url', $url));

    $receiver = new Receiver(new DeviceToken($creds['deviceToken'] ?? ''), 'me.Booij.F1-Docs');

    $sender->send($receiver, $notification, true);

    $protocol->closeConnection();
}
