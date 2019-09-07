<?php
declare(strict_types=1);

namespace FIADocumentAPI\Tests\Infrastructure;

use FIADocumentAPI\Application\DocumentCollection;
use FIADocumentAPI\Application\UnableToFetchDocumentsException;
use FIADocumentAPI\Infrastructure\HTMLResponseParserInterface;
use FIADocumentAPI\Infrastructure\HttpDocumentRepository;
use FIADocumentAPI\Infrastructure\UnableToParseHTMLResponseException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \FIADocumentAPI\Infrastructure\HttpDocumentRepository
 */
class HttpDocumentRepositoryTest extends TestCase
{
    /** @var ClientInterface|ObjectProphecy */
    private $client;

    /** @var HttpDocumentRepository */
    private $repository;

    /** @var HTMLResponseParserInterface|ObjectProphecy */
    private $responseParser;

    public function setUp(): void
    {
        $this->client = $this->prophesize(ClientInterface::class);

        $this->responseParser = $this->prophesize(HTMLResponseParserInterface::class);
        $this->responseParser->parse('response-body')
                       ->willReturn([
                           (new DocumentCollection('Test Event')),
                           (new DocumentCollection('Test Event 2')),
                       ]);

        $this->repository = new HttpDocumentRepository(
            $this->client->reveal(),
            $this->responseParser->reveal()
        );
    }

    public function testGetAllReturnsAllDocuments(): void
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn('response-body');

        $this->client->request(Argument::type('string'), 'https://www.fia.com/documents', Argument::type('array'))
                     ->willReturn($response);

        $documents = $this->repository->getAll();

        $this->assertCount(2, $documents);
    }

    public function testGetAllThrowsProperExceptionOnRequestFailure(): void
    {
        $this->expectException(UnableToFetchDocumentsException::class);

        $clientException = new ClientException('', $this->prophesize(RequestInterface::class)->reveal());

        $this->client->request(Argument::cetera())
            ->willThrow($clientException);

        $this->repository->getAll();
    }

    public function testGetAllThrowsProperExceptionWhenUnableToParse(): void
    {
        $this->expectException(UnableToFetchDocumentsException::class);

        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn('response-body');

        $this->client->request(Argument::type('string'), 'https://www.fia.com/documents', Argument::type('array'))
                     ->willReturn($response);

        $this->responseParser->parse('response-body')
            ->willThrow(new UnableToParseHTMLResponseException);

        $this->repository->getAll();
    }

    public function testGetAllThrowsProperExceptionWhenUpstreamReturnsNon200Status(): void
    {
        $this->expectException(UnableToFetchDocumentsException::class);

        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(500);

        $this->client->request(Argument::type('string'), 'https://www.fia.com/documents', Argument::type('array'))
                     ->willReturn($response);

        $this->repository->getAll();
    }
}
