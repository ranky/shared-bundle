<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Domain\ValueObject;


use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Tests\Domain\PageFactory;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\PageCollection;

class CollectionTest extends TestCase
{

    public function testItShouldCreateValidCollection(): void
    {
        $pages          = PageFactory::random(10);
        $pageCollection = new PageCollection($pages);

        $this->assertCount(10, $pageCollection);
        $this->assertEquals($pages[1], $pageCollection->next());
        $this->assertEquals($pages[2], $pageCollection->next());
        $this->assertEquals($pages[0], $pageCollection->first());
        $this->assertEquals($pages[9], $pageCollection->last());
        $pageCollection->rewind();
        $this->assertEquals($pages[0], $pageCollection->current());
        $pageCollection->next();
        $this->assertEquals(1, $pageCollection->key());
        $newPage = PageFactory::random()[0];
        $pageCollection->add($newPage);
        $this->assertEquals($newPage, $pageCollection->last());
        $pageCollection->removeElement($newPage);
        $this->assertNotEquals($newPage, $pageCollection->last());
    }
}
