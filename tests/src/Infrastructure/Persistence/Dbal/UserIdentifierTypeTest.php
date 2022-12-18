<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Infrastructure\Persistence\Dbal;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;
use Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types\UserIdentifierType;

class UserIdentifierTypeTest extends TestCase
{

    /**
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function testItShouldGetUserIdentifierType(): void
    {
        $userIdentifier     = UserIdentifier::fromString('jcarlos');
        $userIdentifierType = new class extends UserIdentifierType {};

        $this->assertSame(
            'VARCHAR(255)',
            $userIdentifierType->getSQLDeclaration([], new MySqlPlatform())
        );

        $this->assertEquals(
            $userIdentifier,
            $userIdentifierType->convertToPHPValue($userIdentifier->value(), new MySqlPlatform())
        );

        $this->assertEquals(
            $userIdentifier->value(),
            $userIdentifierType->convertToDatabaseValue($userIdentifier, new MySqlPlatform())
        );
    }
}
