<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Persistence\Dbal\Types;

use Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types\UuidType;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\PageUuidValueObject;

class PageUuidType extends UuidType
{
    protected function getClass(): string
    {
        return PageUuidValueObject::class;
    }
}
