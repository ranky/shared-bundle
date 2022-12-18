<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\ValueObject;

/**
 * @template T of object
 * @template-implements \IteratorAggregate<int|string, T>
 */
abstract class Collection implements \Countable, \IteratorAggregate, \JsonSerializable
{

    /**
     * @param array<T> $items
     */
    public function __construct(protected array $items = [])
    {
        $this->allIsInstanceOf($items);
    }


    abstract protected function type(): string;

    /**
     * @return array<int|string, array<string, mixed>>
     */
    abstract public function toArray(): array;

    /**
     * @param array<string, mixed> $data
     * @return self<T>
     */
    abstract public static function fromArray(array $data): self;


    /**
     * @return \Traversable<T>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items());
    }

    public function count(): int
    {
        return \count($this->items());
    }

    /**
     * @return array<T>
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @return array<int|string, array<string, mixed>>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return T|null
     */
    public function findByFieldAndValue(string $field, mixed $value): ?object
    {
        $values = \array_column($this->items, $field);
        if (!$values) {
            return null;
        }
        $key = \array_search($value, $values, true);

        return $key ? $this->items[$key] : null;
    }

    /**
     * @param T $item
     * @return void
     */
    public function add(mixed $item): void
    {
        $this->isInstanceOf($item);
        $this->items[] = $item;
    }

    /**
     * @return T|mixed
     */
    public function first(): mixed
    {
        return \reset($this->items);
    }

    /**
     * @return T|mixed
     */
    public function last(): mixed
    {
        return \end($this->items);
    }

    /**
     * Current Key
     * @return string|int|null
     */
    public function key(): string|int|null
    {
        return \key($this->items);
    }

    /**
     * @return T|mixed
     */
    public function next(): mixed
    {
        return \next($this->items);
    }

    /**
     * @param T|mixed $item
     * @return void
     */
    public function removeElement(mixed $item): void
    {
        $key = array_search($item, $this->items, true);
        if ($key){
            $this->remove($key);
        }
    }

    /**
     * @param int|string $key
     * @return void
     */
    public function remove(int|string $key): void
    {
        if (\array_key_exists($key, $this->items)) {
            unset($this->items[$key]);
        }
    }

    public function replace(string|int $key, mixed $item): void
    {
        if (\array_key_exists($key, $this->items)) {
            $this->items[$key] = $item;
        }
    }

    /**
     * @return T|mixed
     */
    public function current(): mixed
    {
        return \current($this->items);
    }

    /**
     * @return T|mixed
     */
    public function rewind(): mixed
    {
        return \reset($this->items);
    }


    public function isInstanceOf(mixed $item): void
    {
        $requiredElementType = $this->type();
        if (!$item instanceof $requiredElementType) {
            throw new \InvalidArgumentException(
                sprintf('The item <%s> is not an instance of <%s>', $item, $requiredElementType)
            );
        }
    }

    /**
     * @param array<T> $items
     * @return void
     */
    protected function allIsInstanceOf(array $items): void
    {
        foreach ($items as $item) {
            $this->isInstanceOf($item);
        }
    }
}
