<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Ranky\SharedBundle\Common\ClassHelper;
use Ranky\SharedBundle\Common\TextHelper;
use Ranky\SharedBundle\Domain\ValueObject\UlidValueObject;

abstract class UlidType extends Type
{
    abstract protected function getClass(): string;

    public function getName(): string
    {
        return TextHelper::snakeCase(ClassHelper::className($this->getClass()));
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        if ($this->hasNativeGuidType($platform)) {
            return $platform->getGuidTypeDeclarationSQL($column);
        }

        return $platform->getBinaryTypeDeclarationSQL([
            'length' => '16',
            'fixed' => true,
        ]);
    }

    /**
     * @param $value
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return UlidValueObject|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?UlidValueObject
    {
        if ($value instanceof UlidValueObject || null === $value) {
            return $value;
        }

        $className = $this->getClass();
        /** @var UlidValueObject $className */

        return $className::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if (null === $value) {
           return null;
        }

        if (!$value instanceof UlidValueObject) {
            $className = $this->getClass();
            /** @var UlidValueObject $className */
            $value = $className::fromString($value);
        }

        return $value->asBinary();
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    private function hasNativeGuidType(AbstractPlatform $platform): bool
    {
        // Compatibility with DBAL < 3.4
        $method = method_exists($platform, 'getStringTypeDeclarationSQL')
            ? 'getStringTypeDeclarationSQL'
            : 'getVarcharTypeDeclarationSQL';

        return $platform->getGuidTypeDeclarationSQL([]) !== $platform->$method(['fixed' => true, 'length' => 36]);
    }
}
