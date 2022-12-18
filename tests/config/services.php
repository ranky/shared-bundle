<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;



use Ranky\SharedBundle\Domain\Site\SiteUrlResolverInterface;
use Ranky\SharedBundle\Infrastructure\Site\SiteUrlResolver;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services()
        ->defaults()
        ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc
        ->autowire(); // Automatically injects dependencies in your services

    $configurator->parameters()->set('site_url', '%env(resolve:SITE_URL)%');

  // For KernelInterface in AbstractApiContext
  $services
        ->load('Ranky\\SharedBundle\\Tests\\Presentation\\Behat\\', '../src/Presentation/Behat/*');

    $services->set(SiteUrlResolver::class);
    $services->alias(SiteUrlResolverInterface::class, SiteUrlResolver::class)->public();
};
