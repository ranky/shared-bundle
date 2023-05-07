<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Common;

use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Tests\Domain\PageFactory;
use Ranky\SharedBundle\Common\ClassHelper;

class ClassHelperTest extends TestCase
{


    /**
     * @dataProvider dataProviderClassName
     * @param string|object $input
     * @param string $expected
     * @return void
     */
    public function testItShouldGiveClassName(string|object $input, string $expected): void
    {
        $this->assertEquals(ClassHelper::className($input), $expected);
    }

    /**
     * @return array<int, array{string|object, string}>
     */
    public function dataProviderClassName(): array
    {
        return [
            ['\Ranky\SharedBundle\Common\ClassHelper', 'ClassHelper'],
            ['\\Ranky\\SharedBundle\\Common\\ClassHelper', 'ClassHelper'],
            ['\Ranky\SharedBundle\Common\ClassHelper::className', 'ClassHelper'],
            [ClassHelper::class, 'ClassHelper'],
            [new ClassHelper(), 'ClassHelper'],
        ];
    }

    /**
     * @dataProvider dataProviderObjectToArray
     *
     * @param object $input
     * @param array<string, mixed> $expected
     * @return void
     * @throws \ReflectionException
     */
    public function testItShouldConvertObjectToArray(object $input, array $expected): void
    {
        $this->assertSame(ClassHelper::objectToArray($input), $expected);
    }

    /**
     * @return array<int, mixed>
     */
    public function dataProviderObjectToArray(): array
    {
        $page = PageFactory::create(1, 'this is a title', 'this is a description');

        return [
            [
                $page,
                [
                    'id' => 1,
                    'title' => 'this is a title',
                    'description' => 'this is a description',
                    'pageUlid' => null,
                    'pageUuid' => null,
                    'pageCollection' => null,
                ],
            ],
        ];
    }

}
