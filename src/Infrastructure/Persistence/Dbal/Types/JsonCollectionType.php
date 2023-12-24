<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use Ranky\SharedBundle\Domain\ValueObject\Collection;

/**
 * @template T of object
 */
abstract class JsonCollectionType extends JsonType
{
    abstract protected function fieldName(): string;

    abstract protected function collectionClass(): string;

    public function getName(): string
    {
        return $this->fieldName();
    }

    /**
     * @param $value
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return Collection<T>
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): Collection
    {
        $items = parent::convertToPHPValue($value, $platform);

        if (!$this->collectionClass()) {
            return $items;
        }

        /*  @var \Ranky\SharedBundle\Domain\ValueObject\Collection $parentClass */
        $parentClass = new ($this->collectionClass());

        if (!$items) {
            return $parentClass;
        }

        /** @phpstan-ignore-next-line */
        return $parentClass::fromArray($items);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
