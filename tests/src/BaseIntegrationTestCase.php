<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BaseIntegrationTestCase extends KernelTestCase
{

    protected function setUp(): void
    {
        self::bootKernel();
    }

    protected function container(): ContainerInterface
    {
        return static::getContainer();
    }

    /**
     * @template T of object
     * @param class-string<T> $classOrId
     * @return T
     */
    protected function getService(string $classOrId): object
    {
        return static::getContainer()->get($classOrId);
    }

    /**
     * @template T of object
     * @param class-string<T> $classOrId
     * @return T
     */
    protected static function service(string $classOrId): object
    {
        return static::getContainer()->get($classOrId);
    }


    protected function getDummyDir(): string
    {
        return $this->container()->getParameter('kernel.project_dir').'/dummy';
    }

    protected static function getDoctrineManager(): EntityManager
    {
        return self::service('doctrine')->getManager();
    }

    protected static function resetTableByClassName(string $className): void
    {
        self::getDoctrineManager()
            ->createQueryBuilder()
            ->from($className, 'className')
            ->delete()
            ->getQuery()
            ->execute();
        self::getDoctrineManager()->flush();
    }

    protected static function clearUnitOfWork(): void
    {
        self::getDoctrineManager()->clear();
    }

}
