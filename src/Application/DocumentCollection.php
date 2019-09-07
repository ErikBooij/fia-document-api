<?php
declare(strict_types=1);

namespace FIADocumentAPI\Application;

use FIADocumentApi\Domain\Document;
use FIADocumentAPI\Domain\Event;
use JsonSerializable;

final class DocumentCollection implements JsonSerializable
{
    /** @var Document[]  */
    private $documents = [];

    /** @var string */
    private $event;

    /**
     * @param string $event
     */
    public function __construct(string $event)
    {
        $this->event = $event;
    }

    /**
     * @return Document[]
     */
    public function getDocuments(): array
    {
        return $this->documents;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @param array $documents
     *
     * @return self
     */
    public function withDocuments(array $documents): self
    {
        $clone = clone $this;

        foreach ($documents as $document) {
            if (!$document instanceof Document) {
                continue;
            }

            $clone->documents[] = $document;
        }

        usort($clone->documents, function (Document $a, Document $b): int {
            // Multiply by -1 to reverse sort
            return ($a->getPublished() <=> $b->getPublished()) * -1;
        });

        return $clone;
    }

    /**
     * @param Document $document
     *
     * @return self
     */
    public function withDocument(Document $document): self
    {
        return $this->withDocuments([$document]);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'event' => $this->event,
            'documents' => $this->documents,
        ];
}}
