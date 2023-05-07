<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Infrastructure\Persistence\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Ranky\SharedBundle\Common\ClassHelper;
use Ranky\SharedBundle\Domain\ValueObject\UidValueObject;

class UidMapperPlatform
{


    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function convertToDatabaseValue(UidValueObject $uid): string
    {
        $platform        = $this->entityManager->getConnection()->getDatabasePlatform();
        $className       = \strtolower(ClassHelper::className($platform::class));
        $toString        = \str_contains($className, 'postgres') ? 'asRfc4122' : 'asBinary';

        return $uid->$toString();
    }
}
