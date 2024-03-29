<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Common;


class ClassHelper
{

    /**
     * @param string|object $class
     *
     * @return string
     */
    public static function className(string|object $class): string
    {
        $className = \is_object($class) ? $class::class : $class;

        $hasNamespace = \strrpos($className, '\\');
        if ($hasNamespace) {
            $className = \substr($className, $hasNamespace + 1);
        }

        $hasClassMethod = \strrpos($className, '::');
        if ($hasClassMethod) {
            $className = \substr($className, 0, $hasClassMethod);
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
