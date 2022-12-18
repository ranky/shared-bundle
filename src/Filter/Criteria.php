<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Filter;

use Ranky\SharedBundle\Common\ClassHelper;
use Ranky\SharedBundle\Filter\Exception\CriteriaException;
use Ranky\SharedBundle\Filter\Exception\FilterException;
use Ranky\SharedBundle\Filter\Exception\OperatorException;
use Ranky\SharedBundle\Filter\Order\OrderBy;
use Ranky\SharedBundle\Filter\Pagination\OffsetPagination;
use Ranky\SharedBundle\Filter\Visitor\CriteriaFilterVisitor;

/**
 * Criteria Filter with pagination and order
 *
 * Example by URL
 *  * url?page[number]=1&sort[direction]=ASC
 *  * page[limit]=20
 *  * sort[field]=foo
 *  * filters[foo]=bar
 *  * filters[foo][starts]=bar
 *  * filters[foo][eq]=bar
 *  * filters[date][eq]=2022-10 with custom query filter
 *  * filters[foo][like]=bar
 *  * filters[foo][in]=bar,baz
 *  * filter=(eq(id,317) and eq('title','bar') or eq('title','baz')) or like('title','shazam@gmail.com')
 *
 * Operator list  {@see \Ranky\SharedBundle\Filter\ConditionOperator}
 */
abstract class Criteria
{

    public const DEFAULT_PAGINATION_LIMIT = 10;
    public const DEFAULT_PAGINATION_RANGE = 2;
    public const DEFAULT_ORDER_FIELD      = 'createdAt';
    public const DEFAULT_ORDER_DIRECTION  = 'DESC';
    public const DEFAULT_ALL_VALUE        = 'all';

    /**
     * @var array<Filter>
     */
    private array $filters;

    private OffsetPagination $offsetPagination;

    private OrderBy $orderBy;

    /**
     * @return array<string>
     */
    abstract public static function normalizeNameFields(): array;

    /**
     * @return array<string, mixed>
     */
    abstract public static function normalizeValues(): array;

    abstract public static function modelClass(): string;

    abstract public static function modelAlias(): string;

    /**
     * @param array<Filter> $filters
     * @param OffsetPagination $offsetPagination
     * @param OrderBy $orderBy
     */
    public function __construct(
        array $filters,
        OffsetPagination $offsetPagination,
        OrderBy $orderBy
    ) {
        \array_map(
            static fn($filter) => $filter instanceof Filter || throw FilterException::notInstanceOf($filter),
            $filters
        );
        $this->filters          = \array_map(
            fn($filter) => $this->normalizeFilterVisitor($filter),
            $filters
        );
        $this->orderBy          = $orderBy->withField(self::normalizeField($orderBy->field()));
        $this->offsetPagination = $offsetPagination;
    }

    /**
     * @return array<Filter>
     */
    public function filters(): array
    {
        return $this->filters;
    }

    public function addFilter(Filter $filter): void
    {
        $this->filters[] = $this->normalizeFilterVisitor($filter);
    }

    private function normalizeFilterVisitor(Filter $filter): Filter
    {
        $filter->accept(new CriteriaFilterVisitor(), $this);

        return $filter;
    }


    public function offsetPagination(): OffsetPagination
    {
        return $this->offsetPagination;
    }

    public function orderBy(): OrderBy
    {
        return $this->orderBy;
    }

    public static function default(): static
    {
        $offsetPagination = new OffsetPagination(1, static::DEFAULT_PAGINATION_LIMIT);
        $orderBy          = new OrderBy(static::DEFAULT_ORDER_FIELD, static::DEFAULT_ORDER_DIRECTION);

        return new static([], $offsetPagination, $orderBy);
    }

    public static function normalizeField(string $field): string
    {
        $normalizedNameFields = static::normalizeNameFields();
        if (\array_key_exists($field, $normalizedNameFields)) {
            return $normalizedNameFields[$field];
        }

        return $field;
    }

    public static function normalizeValue(string $field, mixed $value): mixed
    {
        $normalizedValues = static::normalizeValues();
        if (\array_key_exists($field, $normalizedValues)) {
            return $normalizedValues[$field]($value);
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $filtersData
     * @param array<int|string,mixed> $pagination
     * @param array<int|string,string> $orderBy
     * @return static
     */
    public static function fromRequest(array $filtersData = [], array $pagination = [], array $orderBy = []): static
    {
        $filters = self::filtersFromRequest($filtersData);

        $pagination = [
            'number' => isset($pagination['number']) ? (int)$pagination['number'] : 1,
            'limit' => isset($pagination['limit']) ? (int)$pagination['limit'] : static::DEFAULT_PAGINATION_LIMIT,
            'range' => isset($pagination['range']) ? (int)$pagination['range'] : static::DEFAULT_PAGINATION_RANGE,
            'disable' => isset($pagination['disable']) && $pagination['disable'],
        ];

        $orderBy = [
            'field' => $orderBy['field'] ?? static::DEFAULT_ORDER_FIELD,
            'direction' => $orderBy['direction'] ?? static::DEFAULT_ORDER_DIRECTION,
        ];

        if (!\array_key_exists($orderBy['field'], static::normalizeNameFields())) {
            throw CriteriaException::notValidField($orderBy['field'], ClassHelper::className(static::class));
        }

        $offsetPagination = OffsetPagination::fromRequest($pagination);
        $order            = OrderBy::fromRequest($orderBy);



        return new static($filters, $offsetPagination, $order);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<Filter>
     */
    private static function filtersFromRequest(array $data): array
    {
        /**
         * ```
         * [
         *   'title' => [
         *      'like' => 'title',
         *   ],
         *   'description' => 'description',
         *  ]
         * ```
         */
        $filters = [];
        foreach ($data as $field => $operatorAndValue) {
            if (!\array_key_exists($field, static::normalizeNameFields())) {
                throw CriteriaException::notValidField($field, ClassHelper::className(static::class));
            }

            $operator = \is_array($operatorAndValue)
                ? (string)\key($operatorAndValue)
                : ConditionOperator::DEFAULT_OPERATOR;

            if (($filterOperator = ConditionOperator::tryFrom($operator)) === null) {
                throw OperatorException::notValidFilterOperator($operator);
            }

            $value = \is_array($operatorAndValue) ? \array_values($operatorAndValue)[0] : $operatorAndValue;
            if (empty($value) || $value === self::DEFAULT_ALL_VALUE) {
                continue;
            }

            $filters[] = new ConditionFilter($field, $filterOperator, $value);
        }

        return $filters;
    }

}
