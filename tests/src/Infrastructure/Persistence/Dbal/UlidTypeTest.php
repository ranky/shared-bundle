<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Infrastructure\Persistence\Dbal;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Domain\ValueObject\DomainEventId;
use Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types\UlidType;

class UlidTypeTest extends TestCase
{
    /**
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function testItShouldGetUlidType(): void
    {
        $ulid     = DomainEventId::generate(); // Use any Value Object extending from UlidValueObject
        $ulidType = new class extends UlidType {
            protected function getClass(): string
            {
                return DomainEventId::class;
            }
        };

        $this->assertSame(
            'BINARY(16)',
            $ulidType->getSQLDeclaration([], new MySqlPlatform())
        );

        $this->assertEquals(
            $ulid,
            $ulidType->convertToPHPValue($ulid->asString(), new MySqlPlatform())
        );

        $this->assertEquals(
            $ulid->asBinary(),
            $ulidType->convertToDatabaseValue($ulid, new MySqlPlatform())
        );
    }
}
