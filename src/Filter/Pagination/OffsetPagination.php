<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\Pagination;

class OffsetPagination
{
    public const DEFAULT_PAGINATION_LIMIT = 10;
    public const DEFAULT_PAGINATION_RANGE = 2;

    private int $page;
    private int $limit;
    private int $range;
    private bool $disable;

    public function __construct(int $page, int $limit, int $range = self::DEFAULT_PAGINATION_RANGE, bool $disable = false)
    {
        $this->page  = \max(1, \abs($page));
        $this->limit = \abs($limit);
        $this->range = \abs($range);
        $this->disable = $disable;
    }

    public function isDisable(): bool
    {
        return $this->disable;
    }

    public function disable(): void
    {
        $this->disable = true;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function range(): int
    {
        return $this->range;
    }

    /**
     * @param array<string,mixed> $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            $data['number'] ?? 1,
            $data['limit'] ?? self::DEFAULT_PAGINATION_LIMIT,
            $data['range'] ?? self::DEFAULT_PAGINATION_RANGE,
            $data['disable'] ?? false
        );
    }

}
