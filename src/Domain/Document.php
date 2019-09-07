<?php
declare(strict_types=1);

namespace FIADocumentAPI\Domain;

use DateTime;
use DateTimeInterface;
use JsonSerializable;

/**
 * @codeCoverageIgnore
 */
final class Document implements JsonSerializable
{
    /** @var string */
    private const FILE_LINK_PREFIX = 'https://www.fia.com';

    /** @var string */
    private $event;

    /** @var DateTimeInterface */
    private $published;

    /** @var string */
    private $title;

    /** @var string */
    private $url;

    /**
     * @param string            $title
     * @param DateTimeInterface $published
     * @param string        $event
     * @param string            $url
     */
    public function __construct(string $title, DateTimeInterface $published, string $event, string $url)
    {
        $this->title = $title;
        $this->published = $published;
        $this->event = $event;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @return DateTimeInterface
     */
    public function getPublished(): DateTimeInterface
    {
        return $this->published;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'event' => $this->event,
            'published' => $this->published->format(DateTime::ATOM),
            'title' => $this->title,
            'url' => self::FILE_LINK_PREFIX . $this->url,
        ];
}}
