<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Application\Dto;


interface ResponseDtoInterface extends \JsonSerializable
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
