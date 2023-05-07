<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Infrastructure\Persistence\Dbal;


use Doctrine\DBAL\Platforms\MySQLPlatform;
use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Tests\Domain\PageFactory;
use Ranky\SharedBundle\Infrastructure\Persistence\Dbal\Types\JsonCollectionType;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\PageCollection;

class JsonCollectionTypeTest extends TestCase
{
    /**
     * @throws \JsonException
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function testItShouldGetJsonCollectionType(): void
    {
        $jsonCollectionType = new class extends JsonCollectionType {
            protected function fieldName(): string
            {
                return 'custom_json_collection';
            }

            protected function collectionClass(): string
            {
                return PageCollection::class;
            }
        };

        $this->assertSame(
            'LONGTEXT',
            $jsonCollectionType->getSQLDeclaration([], new MySqlPlatform())
        );
        $pages = PageFactory::random(10);
        $pageCollection = new PageCollection($pages);

        $this->assertEquals(
            $pageCollection,
            $jsonCollectionType->convertToPHPValue(
                \json_encode($pageCollection, \JSON_THROW_ON_ERROR),
                new MySqlPlatform()
            )
        );

        $this->assertSame(
            \json_encode($pageCollection, \JSON_THROW_ON_ERROR),
            $jsonCollectionType->convertToDatabaseValue($pageCollection, new MySqlPlatform())
        );
    }

}
