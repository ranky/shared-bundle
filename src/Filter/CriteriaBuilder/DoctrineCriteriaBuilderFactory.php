<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\CriteriaBuilder;

use Doctrine\ORM\QueryBuilder;
use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\Driver;
use Ranky\SharedBundle\Filter\Visitor\VisitorCollection;

class DoctrineCriteriaBuilderFactory
{

    public function __construct(private readonly VisitorCollection $visitorCollection)
    {
    }

    public function create(QueryBuilder $queryBuilder, Criteria $criteria): DoctrineCriteriaBuilder
    {
        return new DoctrineCriteriaBuilder(
            $queryBuilder,
            $criteria,
            $this->visitorCollection->getVisitorsByDriver(Driver::DOCTRINE_ORM->value)
        );

    }
}
