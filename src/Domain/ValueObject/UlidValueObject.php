<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\ValueObject;


use Symfony\Component\Uid\Ulid;

abstract class UlidValueObject implements \Stringable, \JsonSerializable
{

    private function __construct(protected readonly Ulid $ulid)
    {
    }

    public static function fromString(string $ulid): static
    {
        return new static(Ulid::fromString($ulid));
    }

    public static function fromBinary(string $ulid): static
    {
        return new static(Ulid::fromBinary($ulid));
    }

    public static function generate(): static
    {
        return new static(new Ulid());
    }

    public function asString(): string
    {
        return (string)$this->ulid;
    }

    public function asBinary(): string
    {
        return $this->ulid->toBinary();
    }

    public function equals(self $other): bool
    {
        return $this->asString() === $other->asString();
    }

    public function __toString(): string
    {
        return (string) $this->ulid;
    }

    public function jsonSerialize(): string
    {
        return (string) $this->ulid;
    }

}
