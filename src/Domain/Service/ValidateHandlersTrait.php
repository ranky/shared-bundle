<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\Service;


use Ranky\SharedBundle\Domain\Exception\InvalidHandlerException;

trait ValidateHandlersTrait
{
    /**
     * @template T
     *
     * @param iterable<T> $handlers
     * @param class-string<T> $type
     *
     * @throws InvalidHandlerException
     * @return array<int,T>
     */
    private function validateHandlers(iterable $handlers, string $type): array
    {
        $handlers = $handlers instanceof \Traversable ? \iterator_to_array($handlers) : $handlers;

        \array_map(
            static fn (object $handler) => $handler instanceof $type
                || throw InvalidHandlerException::instanceOf($type, $handler),
            $handlers
        );

        return $handlers;
    }
}
