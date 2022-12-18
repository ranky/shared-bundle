<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\CriteriaBuilder;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Ranky\SharedBundle\Filter\CompositeFilter;
use Ranky\SharedBundle\Filter\CompositeOperator;
use Ranky\SharedBundle\Filter\ConditionFilter;
use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\Visitor\DoctrineExpressionFilterVisitor;
use Ranky\SharedBundle\Filter\Visitor\FilterVisitor;


class DoctrineCriteriaBuilder implements CriteriaBuilder
{

    private Criteria $criteria;
    /** @var FilterVisitor[]  */
    private array $filterVisitors;

    private QueryBuilder $queryBuilder;
    private readonly QueryBuilder $originalQueryBuilder;

    /**
     * @param QueryBuilder $queryBuilder
     * @param Criteria $criteria
     * @param array<FilterVisitor> $filterVisitors
     */
    public function __construct(QueryBuilder $queryBuilder, Criteria $criteria, array $filterVisitors = [])
    {
        $this->queryBuilder         = $queryBuilder;
        $this->originalQueryBuilder = clone $queryBuilder;
        $this->filterVisitors       = $filterVisitors;
        \array_unshift($this->filterVisitors, new DoctrineExpressionFilterVisitor());
        $this->criteria = $criteria;
    }

    public function addFilterVisitor(FilterVisitor $filterVisitor): self
    {
        $this->filterVisitors[] = $filterVisitor;

        return $this;
    }

    public function where(): self
    {
        $this->queryBuilder = clone $this->originalQueryBuilder;

        foreach ($this->criteria->filters() as $filter) {
            foreach ($this->filterVisitors as $filterVisitor) {
                $filter->accept($filterVisitor, $this->criteria);
            }
            if ($filter instanceof ConditionFilter) {
                $this->queryBuilder->andWhere($filter->expression()->getExpression());
            } elseif ($filter instanceof CompositeFilter) {
                match ($filter->operator()) {
                    CompositeOperator::AND => $this->queryBuilder->andWhere($filter->expression()->getExpression()),
                    CompositeOperator::OR => $this->queryBuilder->orWhere($filter->expression()->getExpression()),
                };
            }
            foreach ($filter->expression()->getParameters() as $key => $value) {
                $this->queryBuilder->setParameter($key, $value);
            }
        }

        return $this;
    }

    public function withLimit(): self
    {
        $offsetPagination = $this->criteria->offsetPagination();

        if ($offsetPagination->isDisable()) {
            return $this;
        }

        $this->queryBuilder
            ->setFirstResult(($offsetPagination->page() - 1) * $offsetPagination->limit())
            ->setMaxResults($offsetPagination->limit());

        return $this;
    }

    public function withOrder(): self
    {
        $this->queryBuilder->addOrderBy(
            $this->criteria->orderBy()->field(),
            $this->criteria->orderBy()->direction()
        );

        return $this;
    }

    public function getQuery(): Query
    {
        return $this->queryBuilder->getQuery();
    }

}
