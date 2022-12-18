<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Persistence\ORM\Filter;


use Ranky\SharedBundle\Filter\ConditionFilter;
use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\Driver;
use Ranky\SharedBundle\Filter\Visitor\Extension\FilterExtensionVisitor;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\Page;

class TitleFilterExtensionVisitor implements FilterExtensionVisitor
{

    public function visit(ConditionFilter $filter, Criteria $criteria): ConditionFilter
    {

        $expression = \sprintf('p.title = %s', ':title_visitor');
        $filter->expression()->setExpression($expression);
        $filter->expression()->setParameters([
            ':title_visitor' => $filter->value(),
        ]);

        return $filter;
    }

    public function support(ConditionFilter $filter, Criteria $criteria): bool
    {
        return $filter->field() === 'p.title'
            && \is_a($criteria::modelClass(), Page::class, true);
    }

    public static function driver(): string
    {
        return Driver::DOCTRINE_ORM->value;
    }
}
