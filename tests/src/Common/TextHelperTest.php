<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Common;

use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Common\TextHelper;

class TextHelperTest extends TestCase
{

    /**
     * @dataProvider dataProviderTruncateString
     * @param string $input
     * @param int $length
     * @param string $expected
     * @return void
     */
    public function testItShouldTruncateString(string $input, int $length, string $expected): void
    {
        $this->assertSame($expected, TextHelper::truncate($input, $length));
    }

    /**
     * @return array<int, array{string, int, string}>
     */
    public function dataProviderTruncateString(): array
    {
        return [
            ['this is a string', 30, 'this is a string'],
            ['<p>this is a string with HTML</p>', 30, 'this is a string with HTML'],
            ['<p>this is a string with [shortcode]shortcodes[/shortcode] shortcodes</p>', 200, 'this is a string with shortcodes'],
            ['<p>this is a string with [shortcode] shortcodes</p>', 200, 'this is a string with shortcodes'],
            ['this is a string', 10, 'this is a...'],
        ];
    }

    /**
     * @dataProvider dataProviderTruncateStringWithHTML
     * @param string $input
     * @param int $length
     * @param string $expected
     * @return void
     */
    public function testItShouldTruncateStringWithHTML(string $input, int $length, string $expected): void
    {
        $this->assertSame(TextHelper::truncateWithHTML($input, $length), $expected);
    }

    /**
     * @return array<int, array{string, int, string}>
     */
    public function dataProviderTruncateStringWithHTML(): array
    {
        return [
            ['<p>this is a string with <b>HTML</b> in Málaga</p>', 100, '<p>this is a string with <b>HTML</b> in Málaga</p>'],
            ['<p>this is a string with <b>HTML</b></p>', 10, '<p>this is...'],
        ];
    }



    public function testItShouldConvertStringToSnakeCase(): void
    {
        $this->assertSame(TextHelper::snakeCase('dataProviderSnakeCase'), 'data_provider_snake_case');
    }

    /**
     * @dataProvider dataProviderForHumanString
     * @param string $input
     * @param string $expected
     * @return void
     */
    public function testItShouldConvertToHumanString(string $input, string $expected): void
    {
        $this->assertSame(TextHelper::human($input), $expected);
    }

    /**
     * @return array<int, array{string, string}>
     */
    public function dataProviderForHumanString(): array
    {
        return [
            ['this-is-an-image-name__', 'This is an image name'],
            ['other__name with Málaga', 'Other name with málaga'],
        ];
    }



}
