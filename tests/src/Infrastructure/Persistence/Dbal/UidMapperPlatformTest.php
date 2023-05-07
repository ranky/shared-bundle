<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Infrastructure\Persistence\Dbal;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Ranky\SharedBundle\Domain\ValueObject\UlidValueObject;
use Ranky\SharedBundle\Domain\ValueObject\UuidValueObject;
use Ranky\SharedBundle\Infrastructure\Persistence\Orm\UidMapperPlatform;
use Ranky\SharedBundle\Tests\BaseIntegrationTestCase;

class PostgreSQLPlatform
{
}

class UidMapperPlatformTest extends BaseIntegrationTestCase
{

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testItConvertToMysqlBinaryDatabaseValue(): void
    {
        $uidMapperPlatform = $this->getService(UidMapperPlatform::class);
        $ulid              = UlidValueObject::create();
        $uid               = UuidValueObject::create();
        $this->assertSame($ulid->toBinary(), $uidMapperPlatform->convertToDatabaseValue($ulid));
        $this->assertSame($uid->toBinary(), $uidMapperPlatform->convertToDatabaseValue($uid));
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testItConvertToPostgresStringDatabaseValue(): void
    {
        $entityManager = $this
            ->createMock(EntityManagerInterface::class);

        $connection = $this->createMock(Connection::class);
        $connection
            ->method('getDatabasePlatform')
            ->willReturn(new PostgreSQLPlatform());

        $entityManager
            ->method('getConnection')
            ->willReturn($connection);

        $uidMapperPlatform = new UidMapperPlatform($entityManager);
        $ulid              = UlidValueObject::create();
        $uid               = UuidValueObject::create();
        $this->assertSame($ulid->toRfc4122(), $uidMapperPlatform->convertToDatabaseValue($ulid));
        $this->assertSame($uid->toRfc4122(), $uidMapperPlatform->convertToDatabaseValue($uid));
    }

}
