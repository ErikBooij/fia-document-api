<?php
declare(strict_types=1);

namespace FIADocumentAPI\Tests\Application;

use DateTimeImmutable;
use FIADocumentAPI\Application\DocumentCollection;
use FIADocumentAPI\Domain\Document;
use FIADocumentAPI\Domain\Event;
use FIADocumentAPI\Domain\Season;
use FIADocumentAPI\Domain\Venue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FIADocumentAPI\Application\DocumentCollection
 */
class DocumentCollectionTest extends TestCase
{
    public function testAddDocumentsReturnsProperSortedCollection(): void
    {
        $emptyCollection = new DocumentCollection('Italian Grand Prix');

        $collection = $emptyCollection->withDocuments([
            new Document(
                'Test 1',
                new DateTimeImmutable('2019-01-01T00:00:00+00:00'),
                'Italian Grand Prix',
                'https://test.com/test-1'
            ),
            new Document(
                'Test 2',
                new DateTimeImmutable('2019-01-01T11:00:00+00:00'),
                'Italian Grand Prix',
                'https://test.com/test-2'
            ),
            new Document(
                'Test 3',
                new DateTimeImmutable('2019-01-02T00:00:00+00:00'),
                'Italian Grand Prix',
                'https://test.com/test-3'
            ),
        ]);

        $this->assertNotSame($collection, $emptyCollection);

        $this->assertEquals('Test 3', $collection->getDocuments()[0]->getTitle());
        $this->assertEquals('Test 2', $collection->getDocuments()[1]->getTitle());
        $this->assertEquals('Test 1', $collection->getDocuments()[2]->getTitle());
    }
}
