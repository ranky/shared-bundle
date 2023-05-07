<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;



use Ranky\SharedBundle\Domain\Site\SiteUrlResolver;
use Ranky\SharedBundle\Infrastructure\Persistence\Orm\UidMapperPlatform;
use Ranky\SharedBundle\Infrastructure\Site\SymfonySiteUrlResolver;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\PageRepository;
use Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Persistence\ORM\Repository\DoctrineOrmPageRepository;
use Ranky\SharedBundle\Tests\Dummy\Page\Presentation\PageApiController;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services()
        ->defaults()
        ->public()
        ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc
        ->autowire(); // Automatically injects dependencies in your services

    $configurator->parameters()->set('site_url', '%env(resolve:SITE_URL)%');

    $services
        ->load('Ranky\\SharedBundle\\Tests\\', '../src/*')
        ->exclude([
            '../src/**/*Interface.php',
            '../src/**/*Constraint.php',
            '../src/**/*Request.php',
            '../src/**/*Response.php',
            '../src/**/Domain',
            '../src/Common',
        ]);
    $services
        ->set(PageApiController::class)
        ->tag('controller.service_arguments');

    $services->set(UidMapperPlatform::class);
    $services->set(SymfonySiteUrlResolver::class);
    $services->alias(SiteUrlResolver::class, SymfonySiteUrlResolver::class)->public();

    $services->set(DoctrineOrmPageRepository::class);
    $services->alias(PageRepository::class, DoctrineOrmPageRepository::class)->public();

};
