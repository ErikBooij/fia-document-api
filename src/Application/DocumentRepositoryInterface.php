<?php
declare(strict_types=1);

namespace FIADocumentAPI\Application;

interface DocumentRepositoryInterface
{
    /**
     * @return array
     * @throws UnableToFetchDocumentsException
     */
    public function getAll(): array;
}
