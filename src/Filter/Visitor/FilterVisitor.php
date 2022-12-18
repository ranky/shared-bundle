<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\Visitor;

use Ranky\SharedBundle\Filter\CompositeFilter;
use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\ConditionFilter;
use Ranky\SharedBundle\Filter\Filter;

interface FilterVisitor
{
    public const TAG_NAME = 'ranky.filter_visitor';
    public function visitConditionFilter(ConditionFilter $filter, Criteria $criteria): ConditionFilter;
    public function visitCompositeFilter(CompositeFilter $filter, Criteria $criteria): CompositeFilter;

    public function support(Filter $filter, Criteria $criteria): bool;
}
