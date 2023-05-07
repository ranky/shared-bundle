<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\ValueObject;

interface UidValueObject
{
    public static function create(): static;
    public function asRfc4122(): string;
    public function asBinary(): string;

    public function asString(): string;
}
