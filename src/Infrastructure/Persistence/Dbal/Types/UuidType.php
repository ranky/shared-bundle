<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Ranky\SharedBundle\Domain\ValueObject\UuidValueObject;

abstract class UuidType extends BaseUidType
{
    /**
     * @param $value
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return UuidValueObject|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?UuidValueObject
    {
        if ($value instanceof UuidValueObject || null === $value) {
            return $value;
        }

        $className = $this->getClass();
        /** @var \Ranky\SharedBundle\Domain\ValueObject\UuidValueObject $className */

        return $className::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        $toString = $this->hasNativeGuidType($platform) ? 'toRfc4122' : 'toBinary';

        if ($value instanceof UuidValueObject) {
            return $value->$toString();
        }

        if (null === $value || '' === $value) {
            return null;
        }

        $className = $this->getClass();
        /** @var UuidValueObject $className */
        $value = $className::fromString($value);

        return $value->$toString();
    }
}
