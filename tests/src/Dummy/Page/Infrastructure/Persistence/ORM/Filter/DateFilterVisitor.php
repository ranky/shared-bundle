<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Persistence\ORM\Filter;


use Ranky\SharedBundle\Filter\ConditionFilter;
use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\Filter;
use Ranky\SharedBundle\Filter\Visitor\AbstractFilterVisitor;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\Page;

class DateFilterVisitor extends AbstractFilterVisitor
{

    public function visitConditionFilter(ConditionFilter $filter, Criteria $criteria): ConditionFilter
    {

        $expression = \sprintf('p.createdAt >= %s', ':date_visitor');
        $filter->expression()->setExpression($expression);
        $filter->expression()->setParameters([
            ':date_visitor' => $filter->value(),
        ]);

        return $filter;
    }

    public function support(Filter $filter, Criteria $criteria): bool
    {
        return $filter instanceof ConditionFilter
            && $filter->field() === 'p.createdAt'
            && \is_a($criteria::modelClass(), Page::class, true);
    }
}
