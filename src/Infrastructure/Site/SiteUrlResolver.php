<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Infrastructure\Site;

use Ranky\SharedBundle\Domain\Site\SiteUrlResolverInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class SiteUrlResolver implements SiteUrlResolverInterface
{

    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly RouterInterface $router,
    ) {
    }

    public function siteUrl(?string $path = ''): string
    {
        if ($path) {
            $path = \ltrim($path, '/');
        }

        if ($this->parameterBag->has('site_url')) {
            return \rtrim((string)$this->parameterBag->get('site_url'), '/').'/'.$path;
        }
        $routerContext = $this->router->getContext();
        if ($this->parameterBag->has('router.request_context.scheme')) {
            $routerContext->setScheme((string)$this->parameterBag->get('router.request_context.scheme'));
            $this->router->setContext($routerContext);
        }
        if ($this->parameterBag->has('router.request_context.host')) {
            $routerContext->setHost((string)$this->parameterBag->get('router.request_context.host'));
            $this->router->setContext($routerContext);
        }
        if ($routerContext->getHost()) {
            $url = $routerContext->getScheme().'://'.$routerContext->getHost();

            return \rtrim($url, '/').'/'.$path;
        }
        try {
            if ($this->router->match('/')) {
                $url = $this->router->generate(
                    $this->router->match('/')['_route'],
                    [], // _locale https://symfony.com/doc/6.0/routing.html#generating-urls-in-services
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                return \rtrim($url, '/').'/'.$path;
            }
        } catch (NoConfigurationException|RouteNotFoundException) {
        }

        $url = 'http';
        if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            $url = 'https';
        }
        $url .= '://'.$_SERVER['HTTP_HOST'];
        $url .= \str_replace(\basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

        return \rtrim($url, '/').'/'.$path;
    }
}
