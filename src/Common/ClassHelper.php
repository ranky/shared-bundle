<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Common;


class ClassHelper
{

    /**
     * @see https://stackoverflow.com/a/25472778
     *
     * @param string|object $class
     *
     * @return string
     */
    public static function className(string|object $class): string
    {
        $className = \is_object($class) ? $class::class : $class;

        $hasNamespace = \mb_strrpos($className, '\\');
        if ($hasNamespace) {
            $className = \mb_substr($className, $hasNamespace + 1);
        }

        $hasClassMethod = \mb_strrpos($className, '::');
        if ($hasClassMethod) {
            $className = \mb_substr($className, 0, $hasClassMethod);
        }

        return $className;
    }

    /**
     * @param object $object
     * @throws \ReflectionException
     * @return array<string, mixed>
     */
    public static function objectToArray(object $object): array
    {
        $reflectionClass = new \ReflectionClass($object::class);
        $array           = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            if (!$property->isInitialized($object)){
                continue;
            }
            $value = $property->getValue($object);
            if (\is_object($value)) {
                $array[$property->getName()] = self::objectToArray($value);
            } elseif (\is_array($value) && is_object($value[0])) {
                $array[$property->getName()] = array_map(
                    static fn(object $object) => self::objectToArray($object),
                    $array
                );
            } else {
                $array[$property->getName()] = $value;
            }
            $property->setAccessible(false);
        }

        return $array;
    }

}
