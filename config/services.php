<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;


use Ranky\SharedBundle\Domain\Event\DomainEventPublisher;
use Ranky\SharedBundle\Domain\Event\InMemoryDomainEventPublisher;
use Ranky\SharedBundle\Domain\Site\SiteUrlResolver;
use Ranky\SharedBundle\Filter\Attributes\CriteriaValueResolver;
use Ranky\SharedBundle\Filter\Pagination\OffsetPagination;
use Ranky\SharedBundle\Infrastructure\Persistence\Orm\UidMapperPlatform;
use Ranky\SharedBundle\Infrastructure\Site\SymfonySiteUrlResolver;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc
        ->autowire(); // Automatically injects dependencies in your services

    $services
        ->load('Ranky\\SharedBundle\\', '../src/*')
        ->exclude([
            '../src/{DependencyInjection,DQL,Contract,Common,Helper,Entity,Trait,Traits,Migrations,Tests,RankySharedBundle.php}',
            '../src/Infrastructure/DependencyInjection',
            '../src/Presentation/Exception/ApiProblemLogErrorListener.php',
            '../src/**/*Interface.php',
            '../src/Domain',
            '../src/Application/Dto',
            '../src/Exception/',
            '../src/Common',
            '../src/Filter/Order/OrderBy.php',
        ]);

    $services->set(InMemoryDomainEventPublisher::class);
    $services->alias(DomainEventPublisher::class, InMemoryDomainEventPublisher::class);

    // Behat
    $services
        ->load('Ranky\\SharedBundle\\Presentation\\Behat\\', '../src/Presentation/Behat/*')
        ->exclude([
            '../src/Presentation/Behat/ApiContextTrait.php'
        ]);

    $services->set(SymfonySiteUrlResolver::class);
    $services->alias(SiteUrlResolver::class, SymfonySiteUrlResolver::class);

    /* Criteria: Override service for new $paginationLimit */
    $services->set(CriteriaValueResolver::class)->args([
        '$paginationLimit' => OffsetPagination::DEFAULT_PAGINATION_LIMIT,
    ]);
};
