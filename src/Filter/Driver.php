<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter;

enum Driver: string
{
    case DOCTRINE_ORM = 'DOCTRINE_ORM';
    case SQL = 'SQL';

    /**
     * @return array<string>
     */
    public static function drivers(): array
    {
        return \array_map(static fn(self $driver) => $driver->value, self::cases());
    }

    public static function tryFromName(string $name): ?self
    {
        try {
            return \constant("self::$name");
        } catch (\Throwable) {
            return null;
        }
    }

}
