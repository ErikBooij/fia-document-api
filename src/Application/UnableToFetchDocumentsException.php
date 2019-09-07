<?php
declare(strict_types=1);

namespace FIADocumentAPI\Application;

use Exception;
use Throwable;

class UnableToFetchDocumentsException extends Exception
{
    /**
     * @param string         $message
     * @param Throwable|null $previous
     *
     * @return UnableToFetchDocumentsException
     */
    public static function because(string $message, ?Throwable $previous = null): self
    {
        return new self("Unable to fetch documents from the FIA website because {$message}", 0, $previous);
    }
}
