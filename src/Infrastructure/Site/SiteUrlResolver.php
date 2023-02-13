<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Infrastructure\Site;

use Ranky\SharedBundle\Domain\Site\SiteUrlResolverInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SiteUrlResolver implements SiteUrlResolverInterface
{

    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly RequestStack $requestStack,
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
        if ($this->requestStack->getCurrentRequest()) {
            return $this->requestStack->getCurrentRequest()->getUriForPath($path ?? '/');
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
            $port = '';
            if ('http' === $routerContext->getScheme() && 80 !== $routerContext->getHttpPort()) {
                $port = ':'.$routerContext->getHttpPort();
            } elseif ('https' === $routerContext->getScheme() && 443 !== $routerContext->getHttpsPort()) {
                $port = ':'.$routerContext->getHttpsPort();
            }
            $url = $routerContext->getScheme().'://'.$routerContext->getHost().$port;

            return \rtrim($url, '/').'/'.$path;
        }

        $url = 'http';
        if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            $url = 'https';
        }
        $url .= '://'.$_SERVER['HTTP_HOST'];
        if (isset($_SERVER['SERVER_PORT']) && '80' !== $_SERVER['SERVER_PORT'] && '443' !== $_SERVER['SERVER_PORT']) {
            $url .= ':'.$_SERVER['SERVER_PORT'];
        }
        $url .= \str_replace(\basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

        return \rtrim($url, '/').'/'.$path;
    }
}
