<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\ValueObject;


final class UserIdentifier
{
    public const DEFAULT_USER_IDENTIFIER = 'guest';

    private string $value;

    public function __construct(?string $value)
    {
        $this->value = $value ?? self::DEFAULT_USER_IDENTIFIER;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value();
    }

}
