<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\Visitor\Extension;

use Ranky\SharedBundle\Filter\ConditionFilter;
use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\Filter;
use Ranky\SharedBundle\Filter\Visitor\AbstractFilterVisitor;

class FilterExtensionVisitorFacade extends AbstractFilterVisitor
{


    public function __construct(private readonly FilterExtensionVisitor $filterExtensionVisitor)
    {
    }

    public function visitConditionFilter(ConditionFilter $filter, Criteria $criteria): ConditionFilter
    {
        return $this->filterExtensionVisitor->visit($filter, $criteria);
    }

    public function support(Filter $filter, Criteria $criteria): bool
    {
        return $filter instanceof ConditionFilter &&
            $this->filterExtensionVisitor->support($filter, $criteria);
    }

}
