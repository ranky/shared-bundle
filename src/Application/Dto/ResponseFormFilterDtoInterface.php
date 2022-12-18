<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Application\Dto;


interface ResponseFormFilterDtoInterface extends \JsonSerializable
{
    public function value(): string;

    public function label(): string;

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
