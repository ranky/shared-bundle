<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Presentation\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class EnumTwigExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('enum', [$this, 'createProxy']),
        ];
    }

    /**
     * https://github.com/twigphp/Twig/issues/3681#issuecomment-1162728959
     * @param class-string $enumClass
     * @return object
     */
    public function createProxy(string $enumClass): object
    {
        return new class($enumClass) {
            public function __construct(private readonly string $enum)
            {
                if (!\enum_exists($this->enum)) {
                    throw new \InvalidArgumentException("$this->enum is not an Enum type and cannot be used in this function");
                }
            }

            /**
             * @param string $name
             * @param mixed[] $arguments
             * @return mixed
             */
            public function __call(string $name, array $arguments): mixed
            {
                $enumClass = \sprintf('%s::%s', $this->enum, $name);

                if (\defined($enumClass)) {
                    return \constant($enumClass);
                }

                if (\method_exists($this->enum, $name)) {
                    return $this->enum::$name(...$arguments);
                }

                throw new \BadMethodCallException("Neither \"{$enumClass}\" or \"{$enumClass}::{$name}()\" exist in this runtime.");
            }
        };
    }
}
