<?php
declare(strict_types=1);

namespace FIADocumentAPI\Application;

interface DocumentRepositoryInterface
{
    /**
     * @return DocumentCollection[]
     * @throws UnableToFetchDocumentsException
     */
    public function getAll(): array;
}
