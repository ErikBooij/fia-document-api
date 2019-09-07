<?php
declare(strict_types=1);

namespace FIADocumentAPI\Infrastructure;

use Exception;

/**
 * @codeCoverageIgnore
 */
class UnableToParseHTMLResponseException extends Exception
{
    /**
     * @param string $parserType
     *
     * @return UnableToParseHTMLResponseException
     */
    public static function byParserType(string $parserType, ?\Throwable $previous = null): self
    {
        return new self("Unable to parse HTML response with {$parserType} parser", 0, $previous);
    }
}
