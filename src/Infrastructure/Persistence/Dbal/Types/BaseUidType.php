<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Ranky\SharedBundle\Common\ClassHelper;
use Ranky\SharedBundle\Common\TextHelper;

abstract class BaseUidType extends Type
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

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    protected function hasNativeGuidType(AbstractPlatform $platform): bool
    {
        // Compatibility with DBAL < 3.4
        $method = method_exists($platform, 'getStringTypeDeclarationSQL')
            ? 'getStringTypeDeclarationSQL'
            : 'getVarcharTypeDeclarationSQL';

        return $platform->getGuidTypeDeclarationSQL([]) !== $platform->$method(['fixed' => true, 'length' => 36]);
    }
}
