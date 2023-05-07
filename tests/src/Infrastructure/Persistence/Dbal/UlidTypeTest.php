<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Infrastructure\Persistence\Dbal;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Domain\ValueObject\UlidValueObject;
use Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types\UlidType;

class UlidTypeTest extends TestCase
{
    /**
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function testItShouldGetUlidType(): void
    {
        $ulid     = UlidValueObject::create(); // Use any Value Object extending from UlidValueObject
        $ulidType = new class extends UlidType {
            protected function getClass(): string
            {
                return UlidValueObject::class;
            }
        };

        $this->assertSame(
            'BINARY(16)',
            $ulidType->getSQLDeclaration([], new MySqlPlatform())
        );
        $this->assertSame(
            'UUID',
            $ulidType->getSQLDeclaration([], new PostgreSQLPlatform())
        );

        $this->assertEquals(
            $ulid,
            $ulidType->convertToPHPValue($ulid->asString(), new MySqlPlatform())
        );

        $this->assertEquals(
            $ulid,
            $ulidType->convertToPHPValue($ulid->asRfc4122(), new PostgreSQLPlatform())
        );

        $this->assertEquals(
            $ulid->asBinary(),
            $ulidType->convertToDatabaseValue($ulid, new MySqlPlatform())
        );

        $this->assertEquals(
            $ulid->asRfc4122(),
            $ulidType->convertToDatabaseValue($ulid, new PostgreSQLPlatform())
        );
    }
}
