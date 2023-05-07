<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Persistence\Dbal\Types;

use Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types\JsonCollectionType;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\PageCollection;

/**
 * @extends JsonCollectionType<\Ranky\SharedBundle\Tests\Dummy\Page\Domain\Page>
 */
class PageCollectionType extends JsonCollectionType
{

    protected function fieldName(): string
    {
        return 'page_collection';
    }

    protected function collectionClass(): string
    {
        return PageCollection::class;
    }
}
