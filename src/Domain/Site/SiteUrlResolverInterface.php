<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\Site;

interface SiteUrlResolverInterface
{
    public function siteUrl(?string $path = ''): string;
}
