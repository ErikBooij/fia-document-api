<?php
declare(strict_types=1);

namespace FIADocumentAPI\Infrastructure;

use FIADocumentAPI\Application\DocumentRepositoryInterface;
use FIADocumentAPI\Application\UnableToFetchDocumentsException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

final class HttpDocumentRepository implements DocumentRepositoryInterface
{
    /** @var FileSystemCache */
    private $fileSystemCache;

    /** @var ClientInterface */
    private $httpClient;

    /** @var HTMLResponseParserInterface */
    private $responseParser;

    /**
     * @param ClientInterface             $httpClient
     * @param HTMLResponseParserInterface $responseParser
     * @param FileSystemCache             $fileSystemCache
     */
    public function __construct(
        ClientInterface $httpClient,
        HTMLResponseParserInterface $responseParser,
        FileSystemCache $fileSystemCache
    ) {
        $this->httpClient = $httpClient;
        $this->responseParser = $responseParser;
        $this->fileSystemCache = $fileSystemCache;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        $cacheData = $this->fileSystemCache->get('parsed-data');

        if (is_array($cacheData)) {
            return $cacheData;
        }

        try {
            $response = $this->httpClient->request('GET', 'https://www.fia.com/documents', [
                'max' => 0,
                'connect_timeout' => 1,
                'read_timeout' => 1,
            ]);

            if (($statusCode = $response->getStatusCode()) !== 200) {
                throw UnableToFetchDocumentsException::because("the request returned a {$statusCode} response");
            }

            $parsed = $this->responseParser->parse((string)$response->getBody());

            return $this->fileSystemCache->set('parsed-data', $parsed, 60);
        } catch (GuzzleException $exception) {
            throw UnableToFetchDocumentsException::because("failed to fulfill the request to FIA's upstream", $exception);
        } catch (UnableToParseHTMLResponseException $exception) {
            throw UnableToFetchDocumentsException::because("upstream response could not be parsed");
        }
    }
}
