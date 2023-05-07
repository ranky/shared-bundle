<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Infrastructure\Persistence\Dbal;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Domain\ValueObject\UuidValueObject;
use Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types\UuidType;

class UuidTypeTest extends TestCase
{
    /**
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function testItShouldGetUuidType(): void
    {
        $uuidV4     = UuidValueObject::create(); // Use any Value Object extending from UlidValueObject
        $uuidType = new class extends UuidType {
            protected function getClass(): string
            {
                return UuidValueObject::class;
            }
        };

        $this->assertSame(
            'BINARY(16)',
            $uuidType->getSQLDeclaration([], new MySqlPlatform())
        );

        $this->assertSame(
            'UUID',
            $uuidType->getSQLDeclaration([], new PostgreSQLPlatform())
        );

        $this->assertEquals(
            $uuidV4,
            $uuidType->convertToPHPValue($uuidV4->asString(), new MySqlPlatform())
        );

        $this->assertEquals(
            $uuidV4,
            $uuidType->convertToPHPValue($uuidV4->asRfc4122(), new PostgreSQLPlatform())
        );

        $this->assertEquals(
            $uuidV4->asBinary(),
            $uuidType->convertToDatabaseValue($uuidV4, new MySqlPlatform())
        );

        $this->assertEquals(
            $uuidV4->asRfc4122(),
            $uuidType->convertToDatabaseValue($uuidV4, new PostgreSQLPlatform())
        );
    }
}
