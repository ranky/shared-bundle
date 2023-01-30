<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Common;

use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Common\FileHelper;

class FileHelperTest extends TestCase
{

    /**
     * @dataProvider dataProviderForPathJoin
     * @param string $expected
     * @param string $paths
     * @return void
     */
    public function testItShouldPathJoin(string $expected, ...$paths): void
    {
        $this->assertSame($expected, FileHelper::pathJoin(...$paths));

    }

    /**
     * @return array<int, array{string, string}>
     */
    public function dataProviderForPathJoin(): array
    {
        return [
            ['/foo/bar/baz', 'foo', 'bar', 'baz'],
            ['/foo/bar/baz', '/foo/', 'bar/', '/baz'],
            ['/foo', 'foo'],
            ['/foo/pedro.jpg', '/foo/pedro.jpg'],
            ['/foo/pedro.jpg', '/foo','pedro.jpg'],
        ];
    }

    /**
     * @dataProvider dataProviderForNormalizePath
     * @param string $input
     * @param string $expected
     * @return void
     */
    public function testItShouldNormalizePath(string $input, string $expected): void
    {
        $this->assertSame($expected, FileHelper::normalizeDirectoryPath($input));
    }

    /**
     * @return array<int, array{string, string}>
     */
    public function dataProviderForNormalizePath(): array
    {
        return [
            ['foo','/foo'],
            ['foo/','/foo'],
            ['foo/bar','/foo/bar'],
            ['foo/bar/baz.jpg','/foo/bar'],
        ];
    }

    /**
     * @dataProvider dataProviderForBaseName
     * @param string $input
     * @param string $expected
     * @return void
     */
    public function testItShouldGiveBasenameFromString(string $input, string $expected): void
    {
        $this->assertSame(FileHelper::basename($input), $expected);
    }

    /**
     * @return array<int, array{string, string}>
     */
    public function dataProviderForBaseName(): array
    {
        return [
            ['PHP is impressive','php-is-impressive'],
            ['slug with   multiple space','slug-with-multiple-space'],
            ['slug with dash--and--spaces.jpg','slug-with-dash-and-spaces'],
        ];
    }

    /**
     * @dataProvider dataProviderForHumanFileSize
     * @param int $input (bytes)
     * @param string $expected
     * @return void
     */
    public function testItShouldGiveHumanFileSize(int $input, string $expected): void
    {
        $this->assertSame(FileHelper::humanFileSize($input), $expected);
    }

    /**
     * @return array<int, array{int, string}>
     */
    public function dataProviderForHumanFileSize(): array
    {
        return [
            [100,'100 B'],
            [1000,'0.98 kB'],
            [7_340_032,'7 MB'],
            [1073741824,'1 GB'],
            [10737418240,'10 GB'],
            [1_099_511_627_776,'1 TB'],
        ];
    }

    public function testShouldMakeAndRemoveDirectory(): void
    {
        $tempDirectoryPath = __DIR__.'/temp';
        FileHelper::makeDirectory($tempDirectoryPath);
        $this->assertDirectoryExists($tempDirectoryPath);
        \rmdir($tempDirectoryPath);
        $this->assertDirectoryDoesNotExist($tempDirectoryPath);
    }

    public function testShouldMakeAndRemoveDirectoryRecursive(): void
    {
        $rootTempDirectoryPath = __DIR__.'/temp';
        FileHelper::makeDirectory($rootTempDirectoryPath);
        for ($i=1; $i<5; $i++){
            FileHelper::makeDirectory($rootTempDirectoryPath.'/temp-'.$i);
        }
        FileHelper::removeRecursiveDirectoriesAndFiles($rootTempDirectoryPath);
        $this->assertTrue(FileHelper::isDirectoryEmpty($rootTempDirectoryPath));
        \rmdir($rootTempDirectoryPath);
        $this->assertDirectoryDoesNotExist($rootTempDirectoryPath);
    }

}
