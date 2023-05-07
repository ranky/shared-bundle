<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Infrastructure\Site;

use Ranky\SharedBundle\Domain\Site\SiteUrlResolver;

class ServerSiteUrlResolver implements SiteUrlResolver
{

    public function siteUrl(?string $path = ''): string
    {
        if ($path) {
            $path = \ltrim($path, '/');
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
