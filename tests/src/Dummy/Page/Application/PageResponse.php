<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Dummy\Page\Application;


use Ranky\SharedBundle\Application\Dto\ResponseDtoInterface;
use Ranky\SharedBundle\Common\ClassHelper;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\Page;

class PageResponse implements ResponseDtoInterface
{

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

    public static function fromEntity(Page $page): self
    {
        return new self(
            $page->getId(),
            $page->getTitle(),
            $page->getDescription(),
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
