<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\ValueObject;

abstract class IntValueObject
{
    public function __construct(protected readonly int $value)
    {
    }

    public static function fromInt(int $value): static
    {
        return new static($value);
    }

    public function value(): int
    {
        return $this->value;
    }
}
