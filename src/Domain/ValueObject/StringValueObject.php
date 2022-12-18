<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\ValueObject;

abstract class StringValueObject
{
    public function __construct(protected readonly string $value)
    {
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function value(): string
    {
        return $this->value;
    }
}
