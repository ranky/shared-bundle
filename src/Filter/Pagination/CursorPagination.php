<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\Pagination;


use Traversable;

/**
 * @template T
 * @implements \IteratorAggregate<int,T>
 */
class CursorPagination implements \IteratorAggregate, \JsonSerializable
{

    /** @var array<int,T>  */
    private array $results;
    private int $size;
    private int $count;
    private ?string $cursor;
    private ?string $nextCursor;
    private ?string $prevCursor;

    /**
     * @param array<int,T> $results
     * @param \Ranky\SharedBundle\Filter\Pagination\Cursor $cursor
     */
    public function __construct(array $results, Cursor $cursor)
    {
        $this->results    = \array_slice($results, 0, $cursor->size());
        $this->count      = \count($this->results);
        $this->cursor     = $cursor->value();
        $this->size       = $cursor->size();
        $this->nextCursor = $results[$cursor->size()]['id'] ?? null;
        $this->prevCursor = $cursor->value() ? null : ($results[0]['id'] ?? null);
    }

    public function count(): int
    {
        return $this->count;
    }

    public function size(): int
    {
        return $this->size;
    }


    public function cursor(): ?string
    {
        return $this->cursor;
    }


    public function nextCursor(): ?string
    {
        return $this->nextCursor;
    }

    public function prevCursor(): ?string
    {
        return $this->prevCursor;
    }

    /**
     * @return \Traversable<T>
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->results);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'result'     => $this->results,
            'pagination' => [
                'count'      => $this->count,
                'size'       => $this->size,
                'cursor'     => $this->cursor,
                'nextCursor' => $this->nextCursor,
                'prevCursor' => $this->prevCursor,
            ],
        ];
    }
}
