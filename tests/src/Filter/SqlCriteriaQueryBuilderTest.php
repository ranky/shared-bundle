<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Filter;

use Ranky\SharedBundle\Filter\CompositeFilter;
use Ranky\SharedBundle\Filter\ConditionFilter;
use Ranky\SharedBundle\Filter\CriteriaBuilder\SqlCriteriaBuilder;
use Ranky\SharedBundle\Filter\FilterFactory;
use Ranky\SharedBundle\Filter\Order\OrderBy;
use Ranky\SharedBundle\Filter\Pagination\OffsetPagination;
use Ranky\SharedBundle\Tests\BaseIntegrationTestCase;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\PageCriteria;

class SqlCriteriaQueryBuilderTest extends BaseIntegrationTestCase
{
    public ConditionFilter $likeConditionFilter;
    public ConditionFilter $lessThatConditionFilter;
    public ConditionFilter $startsConditionFilter;
    public CompositeFilter $compositeAndFilter;
    public CompositeFilter $compositeOrFilter;


    protected function setUp(): void
    {
        $this->likeConditionFilter     = FilterFactory::nlike('title', 'shazam2');
        $this->lessThatConditionFilter = FilterFactory::lte('id', 3);
        $this->startsConditionFilter   = FilterFactory::starts('description', 'this is a description');
        $this->compositeAndFilter      = FilterFactory::and($this->likeConditionFilter, $this->lessThatConditionFilter);
        $this->compositeOrFilter       = FilterFactory::or($this->startsConditionFilter, $this->compositeAndFilter);

        parent::setUp();
    }

    public function testItShouldBuildDoctrineQueryWithDoctrineCriteriaQueryBuilder(): void
    {
        $offsetPagination = new OffsetPagination(1, 30);
        $orderBy          = new OrderBy('createdAt', OrderBy::DESC);
        $pageCriteria     = new PageCriteria(
            [$this->compositeOrFilter],
            $offsetPagination,
            $orderBy
        );

        $sqlCriteriaQueryBuilder = new SqlCriteriaBuilder($pageCriteria);
        $sqlQuery                = $sqlCriteriaQueryBuilder
            ->where()
            ->withLimit()
            ->withOrder()
            ->getQuery();


        $this->assertSame(
            '(p.description like "this is a description%" or (p.title not like "%shazam2%" and p.id <= 3))'.
            ' LIMIT 30 OFFSET 0 ORDER BY p.createdAt DESC',
            $sqlQuery
        );
    }

}
