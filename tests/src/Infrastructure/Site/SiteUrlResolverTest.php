<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Infrastructure\Site;

use Ranky\SharedBundle\Infrastructure\Site\SiteUrlResolver;
use Ranky\SharedBundle\Tests\BaseIntegrationTestCase;
use Ranky\SharedBundle\Domain\Site\SiteUrlResolverInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Routing\RouterInterface;

class SiteUrlResolverTest extends BaseIntegrationTestCase
{

    public function testItShouldResolveSiteUrlWithSiteURLWithENVDefined(): void
    {
        $_ENV['SITE_URL'] = 'https://www.clicksolution.es';
        $siteUrlResolver = $this->getService(SiteUrlResolverInterface::class);
        $this->assertSame($_ENV['SITE_URL'].'/', $siteUrlResolver->siteUrl());
        $this->assertSame($_ENV['SITE_URL'].'/new-path', $siteUrlResolver->siteUrl('new-path'));
        $this->assertSame($_ENV['SITE_URL'].'/new-path', $siteUrlResolver->siteUrl('/new-path'));
    }

    public function testItShouldResolveSiteUrlWithSiteURLWithDefaultRouterContext(): void
    {
        $parameterBag = new ParameterBag([]);
        $router = $this->getService(RouterInterface::class);
        $siteUrlResolver = new SiteUrlResolver($parameterBag, $router);
        $this->assertSame('http://localhost/', $siteUrlResolver->siteUrl());
    }

    public function testItShouldResolveSiteUrlWithSiteURLWithRouterContextAndParametersDefined(): void
    {
        $parameterBag = new ParameterBag([]);
        $parameterBag->set('router.request_context.scheme', 'https');
        $router = $this->getService(RouterInterface::class);
        $siteUrlResolver = new SiteUrlResolver($parameterBag, $router);
        $this->assertSame('https://localhost/', $siteUrlResolver->siteUrl());
    }

}
