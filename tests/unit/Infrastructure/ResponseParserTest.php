<?php
declare(strict_types=1);

namespace FIADocumentAPI\Tests\Infrastructure;

use FIADocumentAPI\Infrastructure\ResponseParserXML;
use FIADocumentAPI\Infrastructure\UnableToParseHTMLResponseException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FIADocumentAPI\Infrastructure\ResponseParserXML
 */
class ResponseParserTest extends TestCase
{
    public function testParseShouldReturnDocumentsFromHTML(): void
    {
        try {
            $parser = new ResponseParserXML;
            $documentCollections = $parser->parse(
                file_get_contents(__DIR__ . '/../../sample-data/documents-response.html')
            );
        } catch (UnableToParseHTMLResponseException $exception) {
            $this->fail($exception->getMessage());

            return;
        }

        $this->assertCount(14, $documentCollections);

        $this->assertEquals('Italian Grand Prix', $documentCollections[0]->getEvent());
        $this->assertEquals('Belgian Grand Prix', $documentCollections[1]->getEvent());
        $this->assertEquals('Hungarian Grand Prix', $documentCollections[2]->getEvent());
        $this->assertEquals('German Grand Prix', $documentCollections[3]->getEvent());
        $this->assertEquals('British Grand Prix', $documentCollections[4]->getEvent());
        $this->assertEquals('Austrian Grand Prix', $documentCollections[5]->getEvent());
        $this->assertEquals('French Grand Prix', $documentCollections[6]->getEvent());
        $this->assertEquals('Canadian Grand Prix', $documentCollections[7]->getEvent());
        $this->assertEquals('Monaco Grand Prix', $documentCollections[8]->getEvent());
        $this->assertEquals('Spanish Grand Prix', $documentCollections[9]->getEvent());
        $this->assertEquals('Azerbaijan Grand Prix', $documentCollections[10]->getEvent());
        $this->assertEquals('Chinese Grand Prix', $documentCollections[11]->getEvent());
        $this->assertEquals('Bahrain Grand Prix', $documentCollections[12]->getEvent());
        $this->assertEquals('Australian Grand Prix', $documentCollections[13]->getEvent());

        $this->assertEquals('Race scrutineering', $documentCollections[3]->getDocuments()[4]->getTitle());
        $this->assertEquals('2019-07-28 18:39:00 UTC', $documentCollections[3]->getDocuments()[4]->getPublished()->format('Y-m-d H:i:s e'));
    }
}
