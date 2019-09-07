<?php
declare(strict_types=1);

namespace FIADocumentAPI\Infrastructure;

use FIADocumentAPI\Application\DocumentCollection;
use FIADocumentApi\Document;

interface HTMLResponseParserInterface
{
    /**
     * @param string $html
     *
     * @return DocumentCollection[]
     *
     * @throws UnableToParseHTMLResponseException
     */
    public function parse(string $html): array;
}
