<?php
declare(strict_types=1);

use FIADocumentAPI\Application\DocumentRepositoryInterface;
use FIADocumentAPI\Infrastructure\FileSystemCache;
use FIADocumentAPI\Infrastructure\HTMLResponseParserInterface;
use FIADocumentAPI\Infrastructure\HttpDocumentRepository;
use FIADocumentAPI\Infrastructure\ResponseParserXML;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use League\Container\Container;

$container = new Container;
$container->add(DocumentRepositoryInterface::class, HttpDocumentRepository::class)
          ->addArguments([
              ClientInterface::class,
              HTMLResponseParserInterface::class,
              FileSystemCache::class,
          ]);
$container->add(ClientInterface::class, Client::class);
$container->add(FileSystemCache::class, FileSystemCache::class)
          ->addArgument(__DIR__ . '/../cache');
$container->add(HTMLResponseParserInterface::class, ResponseParserXML::class);

return $container;
