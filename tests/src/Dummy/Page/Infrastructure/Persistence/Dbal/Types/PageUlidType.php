<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Persistence\Dbal\Types;

use Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types\UlidType;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\PageUlidValueObject;

class PageUlidType extends UlidType
{
    protected function getClass(): string
    {
        return PageUlidValueObject::class;
    }
}
