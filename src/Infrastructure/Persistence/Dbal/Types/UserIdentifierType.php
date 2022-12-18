<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;

class UserIdentifierType extends StringType
{
    public function getName(): string
    {
        return 'user_identifier';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?UserIdentifier
    {
        if ($value instanceof UserIdentifier || null === $value) {
            return $value;
        }


        return UserIdentifier::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof UserIdentifier) {
            $value = UserIdentifier::fromString($value);
        }

        return $value->value();
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
