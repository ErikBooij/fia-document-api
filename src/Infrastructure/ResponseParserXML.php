<?php
declare(strict_types=1);

namespace FIADocumentAPI\Infrastructure;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use DOMDocument;
use DOMXPath;
use FIADocumentAPI\Application\DocumentCollection;
use FIADocumentAPI\Domain\Document;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use Throwable;

final class ResponseParserXML implements HTMLResponseParserInterface
{
    /**
     * @inheritDoc
     */
    public function parse(string $html): array
    {
        try {
            $dom = new Dom;
            $dom->load($html);

            $wrappers = $dom->find('.event-wrapper');

            $documentCollections = [];

            foreach ($wrappers as $wrapper) {
                if (!$wrapper instanceof HtmlNode) {
                    continue;
                }


                $titleElement = $wrapper->find('.event-title')[0] ?? null;

                if (!$titleElement instanceof HtmlNode) {
                    continue;
                }

                $event = trim($titleElement->text());

                $documentNodes = $wrapper->find('.document-row');
                $documents = [];

                foreach ($documentNodes as $documentNode) {
                    if (!$documentNode instanceof HtmlNode) {
                        continue;
                    }

                    if (!$url = $this->extractUrl($documentNode)) {
                        continue;
                    }

                    if (!$title = $this->extractTitle($documentNode)) {
                        continue;
                    }

                    if (!$published = $this->extractPublicationDate($documentNode)) {
                        continue;
                    }

                    $documents[] = new Document($title, $published, $event, $url);
                }

                if (count($documents) > 0) {
                    $documentCollections[] = (new DocumentCollection($event))->withDocuments($documents);
                }
            }

            return $documentCollections;
        } catch (Throwable $throwable) {
            throw UnableToParseHTMLResponseException::byParserType('XML', $throwable);
        }
    }

    /**
     * @param HtmlNode $node
     *
     * @return string|null
     */
    private function extractUrl(HtmlNode $node): ?string
    {
        try {
            $url = $node->find('a')[0] ?? null;

            if (!$url instanceof HtmlNode) {
                return null;
            }

            $url = $url->getAttribute('href');

            return empty($url) ? null : $url;
        } catch (ChildNotFoundException $exception) {
            return null;
        }
    }

    /**
     * @param HtmlNode $node
     *
     * @return string|null
     */
    private function extractTitle(HtmlNode $node): ?string
    {
        try {
            $title = $node->find('.title')[0] ?? null;

            if (!$title instanceof HtmlNode) {
                return null;
            }

            $title = trim($title->text(true));

            return empty($title) ? null : $title;
        } catch (ChildNotFoundException $exception) {
            return null;
        }
    }

    /**
     * @param HtmlNode $node
     *
     * @return DateTimeInterface|null
     */
    private function extractPublicationDate(HtmlNode $node): ?DateTimeInterface
    {
        try {
            $published = $node->find('.published')[0] ?? null;

            if (!$published instanceof HtmlNode) {
                return null;
            }

            $published = $published->text(true);

            preg_match('/\d{2}\.\d{2}\.\d{2}\. \d{2}:\d{2}.*$/', $published, $matches);

            if (count($matches) < 1) {
                return null;
            }

            $published = DateTimeImmutable::createFromFormat('d.m.y. H:i e', trim($matches[0]));

            if (!$published) {
                return null;
            }

            return $published->setTimezone(new DateTimeZone('UTC'));
        } catch (ChildNotFoundException $exception) {
            return null;
        }
    }
}
