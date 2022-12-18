<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Dummy\Page\Application;


use Ranky\SharedBundle\Application\Dto\RequestDtoInterface;
use Ranky\SharedBundle\Common\ClassHelper;
use Ranky\SharedBundle\Domain\ValueObject\MappingTrait;

class PageRequest implements RequestDtoInterface
{

    use MappingTrait;

    public function __construct(
        private readonly int $id,
        private readonly string $title,
        private readonly string $description,
    ) {}


    public function id(): int
    {
        return $this->id;
    }


    public function title(): string
    {
        return $this->title;
    }


    public function description(): string
    {
        return $this->description;
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            self::getInt($data, 'id'),
            self::getString($data, 'title'),
            self::getString($data, 'description'),
        );
    }

    /**
     * @throws \ReflectionException
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ClassHelper::objectToArray($this);
    }

    /**
     * @throws \ReflectionException
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
