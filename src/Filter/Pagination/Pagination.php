<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\Pagination;


use Traversable;

/**
 * @template T
 * @implements \IteratorAggregate<int, T>
 */
class Pagination implements \IteratorAggregate, \JsonSerializable
{

    /** @var array<T>  */
    private array $results;
    private int $count;
    private int $total;
    private int $current;
    private int $limit;
    private int $pages;
    private int $range;
    private int $next;
    private int $prev;
    private int $right;
    private int $left;
    private bool $disable;

    /**
     * @param array<T> $results
     * @param int $count
     * @param OffsetPagination $offsetPagination
     */
    public function __construct(array $results, int $count, OffsetPagination $offsetPagination)
    {
        $this->results = $results;
        $this->count   = \count($this->results);
        $this->total   = $count;
        $this->current = $offsetPagination->page();
        $this->limit   = $offsetPagination->limit();
        $this->range   = $offsetPagination->range();
        $this->disable = $offsetPagination->isDisable();
        $this->pages   = (int)\ceil($this->total / $this->limit);
        $this->next    = \min($this->pages, $this->current + 1);
        $this->prev    = \max(1, $this->current - 1);
        $this->right   = \min($this->pages, $this->current + $this->range + 1);
        $this->left    = \max(1, $this->current - $this->range);
    }

    public function total(): int
    {
        return $this->total;
    }

    public function count(): int
    {
        return $this->count;
    }

    public function current(): int
    {
        return $this->current;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function pages(): int
    {
        return $this->pages;
    }

    public function range(): int
    {
        return $this->range;
    }

    public function next(): int
    {
        return $this->next;
    }

    public function prev(): int
    {
        return $this->prev;
    }

    public function right(): int
    {
        return $this->right;
    }

    public function left(): int
    {
        return $this->left;
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
        if ($this->disable) {
            return [
                'result' => $this->results,
            ];
        }

        return [
            'pagination' => [
                'total' => $this->total,
                'count' => $this->count,
                'page'  => $this->current,
                'pages' => $this->pages,
                'limit' => $this->limit,
            ],
            'result'     => $this->results,
        ];
    }
}
