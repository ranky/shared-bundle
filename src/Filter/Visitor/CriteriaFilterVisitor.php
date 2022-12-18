<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\Visitor;

use Ranky\SharedBundle\Filter\CompositeFilter;
use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\ConditionFilter;
use Ranky\SharedBundle\Filter\Filter;

class CriteriaFilterVisitor implements FilterVisitor
{

    public function visitConditionFilter(ConditionFilter $filter, Criteria $criteria): ConditionFilter
    {
        $field = $criteria::normalizeField($filter->field());
        // TODO: Normalize value with or without prefixing. Currently without prefixing
        $value = $criteria::normalizeValue($filter->field(), $filter->value());

        $filter->setValue($value);
        $filter->setField($field);

        return $filter;
    }

    public function visitCompositeFilter(CompositeFilter $filter, Criteria $criteria): CompositeFilter
    {
        return $filter;
    }

    public function support(Filter $filter, Criteria $criteria): bool
    {
        return true;
    }
}
