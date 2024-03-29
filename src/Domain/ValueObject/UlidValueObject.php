<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\ValueObject;


use Symfony\Component\Uid\Ulid;

class UlidValueObject extends Ulid implements UidValueObject
{

    public static function create(): static
    {
        return new static(Ulid::generate());
    }

    public function asRfc4122(): string
    {
        return $this->toRfc4122();
    }

    public function asBinary(): string
    {
        return $this->toBinary();
    }

    public function asString(): string
    {
        return $this->__toString();
    }
}
