<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\Pagination;

class Cursor
{
    public const DIRECTIONS   = ['before', 'after'];
    public const DEFAULT_SIZE = 10;


    public function __construct(
        private readonly string $direction = self::DIRECTIONS[1],
        private readonly int $size = self::DEFAULT_SIZE,
        private readonly ?string $value = null
    ) {
        if (!array_key_exists($direction, self::DIRECTIONS)) {
            throw new \InvalidArgumentException(
                \sprintf(
                    '<<%s>> direction not valid. List of valid directions: %s',
                    $direction,
                    \implode(',', self::DIRECTIONS)
                )
            );
        }
    }

    public function direction(): string
    {
        return $this->direction;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function value(): ?string
    {
        return $this->value;
    }

}
