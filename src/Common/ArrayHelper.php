<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Common;

class ArrayHelper
{
    /**
     * @param array<int|string,mixed> $array
     *
     * @return bool
     */
    public static function isMultidimensionalArray(array $array): bool
    {

        return \count($array) !== \count($array, \COUNT_RECURSIVE);
    }

    /**
     * @param array<mixed> $array
     * @return array<mixed>
     */
    public static function flatten(array $array): array
    {
        return \iterator_to_array(
            new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array))
        );
    }

    /**
     * @param mixed $needle
     * @param array<int|string,mixed> $haystack
     * @param bool $strict
     *
     * @return bool
     */
    public static function inArray(mixed $needle, array $haystack, bool $strict = true): bool
    {
       if (!\is_array($needle) || !self::isMultidimensionalArray($haystack)){
            return \in_array($needle, $haystack, $strict);
        }
        $found = false;
        foreach ($haystack as $item) {
            if (($strict && $item === $needle) || (!$strict && $item === $needle)) {
                $found = true;
                break;
            }
            if (\is_array($item)) {
                $found = self::inArray($needle, $item);
                if($found) {
                    break;
                }
            }
        }
        return $found;
    }

}
