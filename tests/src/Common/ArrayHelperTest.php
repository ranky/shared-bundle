<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Common;

use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Common\ArrayHelper;

class ArrayHelperTest extends TestCase
{

    /**
     * @dataProvider dataProviderForMultidimensionalArray
     * @param array<string|int,mixed> $array
     * @param bool $expected
     * @return void
     */
    public function testItShouldCheckIfIsMultidimensionalArray(array $array, bool $expected): void
    {
        if ($expected === true){
            $this->assertTrue(ArrayHelper::isMultidimensionalArray($array));
        }else{
            $this->assertFalse(ArrayHelper::isMultidimensionalArray($array));
        }
    }

    /**
     * @return array<int, array{array<string|int,mixed>, bool}>
     */
    public function dataProviderForMultidimensionalArray(): array
    {
        return [
            [['name' => 'jcarlos','age' => 35], false],
            [[1,2,3], false],
            [['name' => 'jcarlos', 'friends' => ['carlos','alberto']], true],
            [['name' => 'antonio', 'friends' => ['name' => 'carlos','age' => 89]], true],
        ];
    }

    /**
     * @dataProvider dataProviderForInArray
     * @param mixed $needle
     * @param array<string|int,mixed> $haystack
     * @param bool $strict
     * @param bool $expected
     * @return void
     */
    public function testItShouldCheckInArray(mixed $needle, array $haystack, bool $strict, bool $expected): void
    {
        if ($expected === true){
            $this->assertTrue(ArrayHelper::inArray($needle, $haystack, $strict));
        }else{
            $this->assertFalse(ArrayHelper::inArray($needle, $haystack, $strict));
        }
    }

    /**
     * @return array<int, array{mixed,array<string|int,mixed>, bool, bool}>
     */
    public function dataProviderForInArray(): array
    {
        return [
            [['name' => 'jcarlos','age' => 35],[['name' => 'jcarlos','age' => 35]], false, true],
            [['name' => 'antonio','age' => 25],[['name' => 'jcarlos','brothers' => ['name' => 'antonio', 'age' => 25]]], true, true],
            [['name' => 'roberto','age' => 25],[['name' => 'jcarlos','brothers' => ['name' => 'antonio', 'age' => 25]]], false, false],
            [['name'],[['name' => 'jcarlos','brothers' => ['name' => 'antonio', 'age' => 25]]], false, false],
            ['address',['name','age','address'], false, true],
            [['address'],[['name','age','address'],['address']], false, true],
        ];
    }

}
