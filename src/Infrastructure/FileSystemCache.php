<?php
declare(strict_types=1);

namespace FIADocumentAPI\Infrastructure;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use Exception;

final class FileSystemCache
{
    /** @var string */
    private $directory;

    /**
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    /**
     * @param string $key
     *
     * @return mixed
     * @throws Exception
     */
    public function get(string $key)
    {
        $cacheFile = $this->cacheFile($key);

        if (!file_exists($cacheFile) || !is_readable($cacheFile)) {
            return null;
        }

        if (!($fileContents = @file_get_contents($cacheFile))) {
            return null;
        }

        $data = @unserialize($fileContents);

        if ($data === false && $fileContents !== serialize(false)) {
            return null;
        }

        if (!isset($data['expires'])) {
            return null;
        }

        $expires = new DateTimeImmutable($data['expires']);

        if (!$expires || $expires < new DateTimeImmutable) {
            return null;
        }

        return $data['data'] ?? null;
    }

    /**
     * @param string $key
     * @param mixed  $data
     * @param int    $ttl
     *
     * @return mixed
     * @throws Exception
     */
    public function set(string $key, $data, int $ttl)
    {
        $cacheFile = $this->cacheFile($key);

        $serialized = serialize([
            'expires' => (new DateTimeImmutable)->add(new DateInterval("PT{$ttl}S"))->format(DateTime::ATOM),
            'data' => $data,
        ]);

        @file_put_contents($cacheFile, $serialized);

        return $data;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function cacheFile(string $key): string
    {
        return $this->directory . DIRECTORY_SEPARATOR . $key;
    }
}
