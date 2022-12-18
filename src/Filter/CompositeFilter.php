<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Filter;

use Ranky\SharedBundle\Filter\Visitor\FilterVisitor;

/**
 * @implements \IteratorAggregate<int,Filter>
 */
class CompositeFilter implements Filter, \IteratorAggregate, \Countable
{
    private CompositeOperator $operator;
    private Expression $expression;
    /**
     * @var array<Filter>
     */
    private array $filters;


    public function __construct(CompositeOperator $operator, Filter ...$filters)
    {
        $this->operator = $operator;
        $this->filters  = $filters;
        $this->expression  = $this->createDefaultExpression();
    }


    public function operator(): CompositeOperator
    {
        return $this->operator;
    }

    /**
     * @return array<Filter>
     */
    public function filters(): array
    {
        return $this->filters;
    }

    public function setFilters(Filter ...$filters): void
    {
        $this->filters = $filters;
    }

    public function expression(): Expression
    {
        return $this->expression;
    }

    public function setExpression(Expression $expression): void
    {
        $this->expression = $expression;
    }

    public function accept(FilterVisitor $filterVisitor, Criteria $criteria): void
    {

        $listParameters = [];
        foreach ($this->filters as $filter) {
            $filter->accept($filterVisitor, $criteria);
            $listParameters = [...$listParameters, ...$filter->expression()->getParameters()];
        }
        $this->expression()->setParameters($listParameters);
        $filterVisitor->visitCompositeFilter($this, $criteria);
    }

    private function createDefaultExpression(): Expression
    {
        $listExpressions = \array_map(static fn(Filter $filter) => $filter->expression(), $this->filters());
        $listParameters  = \array_reduce(
            $this->filters(),
            static fn (array $carry, Filter $filter) => [...$carry, ...$filter->expression()->getParameters()],
            []
        );
        return new Expression(
            \implode(' '.$this->operator()->expression().' ', $listExpressions),
            $listParameters
        );
    }

    /**
     * @return \ArrayIterator<int,Filter>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->filters);
    }

    public function count(): int
    {
       return \count($this->filters);
    }


}
