<?php

declare(strict_types=1);

use Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Persistence\Dbal\Types\PageCollectionType;
use Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Persistence\Dbal\Types\PageUlidType;
use Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Persistence\Dbal\Types\PageUuidType;
use Symfony\Config\DoctrineConfig;


return static function (DoctrineConfig $doctrineConfig): void {
    # "TEST_TOKEN" is typically set by ParaTest
    $doctrineConfig->dbal()
        ->defaultConnection('default')
        ->connection('default')
        ->url('%env(resolve:DATABASE_URL)%')
        ->dbnameSuffix('-test%env(default::TEST_TOKEN)%')
        ->logging(false)
        ->charset('utf8');

    $doctrineConfig
        ->dbal()
        ->type('page_uuid', PageUuidType::class)
        ->type('page_ulid', PageUlidType::class)
        ->type('page_collection', PageCollectionType::class);


    $emDefault = $doctrineConfig->orm()->autoGenerateProxyClasses(true)->entityManager('default');
    $emDefault->autoMapping(true);
    $emDefault
        ->mapping('Tests')
        ->dir('%kernel.project_dir%/src/Dummy')
        ->prefix('Ranky\SharedBundle\Tests\Dummy');
};
