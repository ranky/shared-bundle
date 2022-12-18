<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Common;

use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Common\UrlHelper;

class UrlHelperTest extends TestCase
{


    /**
     * @dataProvider dataProviderForSlug
     * @param string $input
     * @param string $expected
     * @return void
     */
    public function testItShouldGiveMeFriendlyUrlSlug(string $input, string $expected): void
    {
        $this->assertEquals(UrlHelper::slug($input), $expected);
    }

    /**
     * @return string[][]
     */
    public function dataProviderForSlug(): array
    {
        return [
            ['/', ''],
            ['',''],
            ['PHP is impressive','php-is-impressive'],
            ['slug with   multiple space','slug-with-multiple-space'],
            ['slug with dash--and--spaces','slug-with-dash-and-spaces'],
            ['SlUG with dash at the end--','slug-with-dash-at-the-end'],
            ['SLUG /// \\ with slashes','slug-with-slashes'],
            ['slug con acentos en Málaga','slug-con-acentos-en-malaga'],
            ['slug with__underscore_-and dash__','slug-with-underscore-and-dash'],
        ];
    }

}
