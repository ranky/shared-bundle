<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Common;


class FileHelper
{

    /**
     * Normalize a non-existing file path into a directory path
     *
     * @param string $path
     * @return string
     */
    public static function normalizeDirectoryPath(string $path): string
    {
        $parts = \explode(\DIRECTORY_SEPARATOR, $path);
        $last = \end($parts);
        if (\str_contains($last, '.')) {
            \array_pop($parts);
        }
        return \DIRECTORY_SEPARATOR.\rtrim(
            \implode(\DIRECTORY_SEPARATOR, $parts),
            \DIRECTORY_SEPARATOR
            );
    }

    /**
     * Join paths
     * @param ...$paths
     * @return string
     */
    public static function pathJoin(...$paths): string
    {
        $cleanPaths = \array_map(static function ($path) {
            return \trim($path, \DIRECTORY_SEPARATOR);
        }, $paths);

        return \sprintf(
            '%s%s',
            \DIRECTORY_SEPARATOR,
            \implode(\DIRECTORY_SEPARATOR, \array_filter($cleanPaths))
        );
    }

    public static function basename(string $fileName): string
    {
        return UrlHelper::slug(\pathinfo($fileName, \PATHINFO_FILENAME));
    }

    public static function humanTitleFromFileName(string $fileName): string
    {
        return TextHelper::human(static::basename($fileName));
    }

    public static function humanFileSize(int $size, int $precision = 2): string
    {
        $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $step  = 1024;
        $i     = 0;
        while (($size / $step) > 0.9) {
            $size /= $step;
            $i++;
        }

        return \round($size, $precision).' '.$units[$i];
    }

    public static function makeDirectory(string $path): bool
    {
        if (\is_dir($path)){
            return true;
        }
        if (!\mkdir($path, 0o755, true) && !\is_dir($path)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $path));
        }

        return true;
    }

    public static function removeRecursiveDirectoriesAndFiles(string $path, bool $removeDotFiles = false): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $path,
                \FilesystemIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        /* @var $fileInfo \RecursiveDirectoryIterator */
        foreach ($files as $fileInfo) {
            if ($fileInfo->getFilename()[0] === '.' && !$removeDotFiles) {
                continue;
            }
            $rmCallable = ($fileInfo->isDir() ? 'rmdir' : 'unlink');
            $rmCallable($fileInfo->getRealPath());
        }
    }

    public static function isDirectoryEmpty(string $directory): bool
    {
        $dirIterator = new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS);
        return \iterator_count($dirIterator) === 0;
    }

}
