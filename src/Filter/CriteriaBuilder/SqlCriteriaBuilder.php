<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\CriteriaBuilder;

use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\Visitor\FilterVisitor;
use Ranky\SharedBundle\Filter\Visitor\SqlExpressionFilterVisitor;

class SqlCriteriaBuilder implements CriteriaBuilder
{

    private Criteria $criteria;
    /** @var FilterVisitor[] */
    private array $filterVisitors;
    private string $query = '';
    private string $limit = '';
    private string $order = '';

    /**
     * @param Criteria $criteria
     * @param array<FilterVisitor> $filterVisitors
     */
    public function __construct(Criteria $criteria, array $filterVisitors = [])
    {
        $this->filterVisitors = $filterVisitors;
        \array_unshift($this->filterVisitors, new SqlExpressionFilterVisitor());
        $this->criteria = $criteria;
    }

    public function addFilterVisitor(FilterVisitor $filterVisitor): self
    {
        $this->filterVisitors[] = $filterVisitor;

        return $this;
    }

    public function where(): self
    {
        $this->query = '';

        foreach ($this->criteria->filters() as $filter) {
            foreach ($this->filterVisitors as $filterVisitor) {
                $filter->accept($filterVisitor, $this->criteria);
            }
            $this->query .= $filter->expression()->getExpression();
        }

        return $this;
    }

    public function withLimit(): self
    {
        $offsetPagination = $this->criteria->offsetPagination();

        $this->limit = \sprintf(
            ' LIMIT %d OFFSET %d',
            $offsetPagination->limit(),
            ($offsetPagination->page() - 1) * $offsetPagination->limit()
        );

        return $this;
    }

    public function withOrder(): self
    {
        $this->order = \sprintf(
            ' ORDER BY %s %s',
            $this->criteria->orderBy()->field(),
            $this->criteria->orderBy()->direction()
        );

        return $this;
    }

    public function getQuery(): string
    {
        if ($this->limit) {
            $this->query .= $this->limit;
        }
        if ($this->order) {
            $this->query .= $this->order;
        }

        return $this->query;
    }

}
