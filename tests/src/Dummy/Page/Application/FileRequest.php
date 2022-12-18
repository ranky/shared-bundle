<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Dummy\Page\Application;

use Ranky\SharedBundle\Application\Dto\RequestDtoInterface;
use Ranky\SharedBundle\Common\ClassHelper;
use Ranky\SharedBundle\Domain\ValueObject\MappingTrait;

final class FileRequest implements RequestDtoInterface
{
    use MappingTrait;

    public function __construct(
        private readonly string $path,
        private readonly string $name,
        private readonly string $mime,
        private readonly string $extension,
        private readonly int $size
    ) {
    }


    public function name(): string
    {
        return $this->name;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function mime(): string
    {
        return $this->mime;
    }

    public function extension(): string
    {
        return $this->extension;
    }

    public function size(): int
    {
        return $this->size;
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            self::getString($data, 'path'),
            self::getString($data, 'name'),
            self::getString($data, 'mime'),
            self::getString($data, 'extension'),
            self::getInt($data, 'size')
        );
    }


    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ClassHelper::objectToArray($this);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
