<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\Visitor;

use Ranky\SharedBundle\Filter\CompositeFilter;
use Ranky\SharedBundle\Filter\ConditionFilter;
use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\Filter;

abstract class AbstractFilterVisitor implements FilterVisitor
{
    abstract public function visitConditionFilter(ConditionFilter $filter, Criteria $criteria): ConditionFilter;
    abstract public function support(Filter $filter, Criteria $criteria): bool;

    public function visitCompositeFilter(CompositeFilter $filter, Criteria $criteria): CompositeFilter
    {
        $listExpressions = \array_map(
            static fn(Filter $filter) => $filter->expression()->getExpression(),
            $filter->filters()
        );
        $filter->expression()->setExpression(
            \sprintf(
                '(%s)',
                \implode(' '.$filter->operator()->expression().' ', $listExpressions)
            )
        );

        return $filter;
    }

    public static function getDefaultPriority(): int
    {
        return 0;
    }
}
