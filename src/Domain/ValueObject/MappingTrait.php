<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\ValueObject;


trait MappingTrait
{
    /**
     * @param array<string, mixed> $data
     * @param string $key
     * @return string
     */
    private static function getString(array $data, string $key): string
    {
        if (!isset($data[$key])) {
            return '';
        }

        return (string)$data[$key];
    }

    /**
     * @param array<string, mixed> $data
     * @param string $key
     * @return bool
     */
    private static function getBool(array $data, string $key): bool
    {
        if (!isset($data[$key])) {
            return false;
        }

        return (bool)$data[$key];
    }

    /**
     * @param array<int|string, mixed> $data
     * @param string $key
     * @return int
     */
    private static function getInt(array $data, string $key): int
    {
        if (!isset($data[$key])) {
            return 0;
        }

        return (int)$data[$key];
    }

    /**
     * @param array<string, mixed> $data
     * @param string $key
     * @return string|null
     */
    private static function getNonEmptyStringOrNull(array $data, string $key): ?string
    {
        if (!isset($data[$key])) {
            return null;
        }
        if ($data[$key] === '') {
            return null;
        }

        return (string)$data[$key];
    }

    /**
     * @param array<string, mixed> $data
     * @param string $key
     * @return string|null
     */
    public static function getFilterValue(array $data, string $key): ?string
    {
        if (!isset($data[$key])) {
            return null;
        }

        return \is_array($data[$key]) ? \array_values($data[$key])[0] : $data[$key];
    }
}
