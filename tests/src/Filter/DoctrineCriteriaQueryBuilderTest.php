<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Filter;

use Ranky\SharedBundle\Filter\CompositeFilter;
use Ranky\SharedBundle\Filter\ConditionFilter;
use Ranky\SharedBundle\Filter\CriteriaBuilder\DoctrineCriteriaBuilder;
use Ranky\SharedBundle\Filter\Driver;
use Ranky\SharedBundle\Filter\FilterFactory;
use Ranky\SharedBundle\Filter\Order\OrderBy;
use Ranky\SharedBundle\Filter\Pagination\OffsetPagination;
use Ranky\SharedBundle\Filter\Visitor\Extension\FilterExtensionVisitorFacade;
use Ranky\SharedBundle\Filter\Visitor\FilterVisitor;
use Ranky\SharedBundle\Filter\Visitor\VisitorCollection;
use Ranky\SharedBundle\Tests\BaseIntegrationTestCase;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\Page;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\PageCriteria;
use Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Persistence\ORM\Filter\DateFilterExtensionVisitor;
use Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Persistence\ORM\Filter\DateFilterVisitor;
use Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Persistence\ORM\Filter\TitleFilterExtensionVisitor;
use Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Persistence\ORM\Filter\TitleFilterVisitor;

class DoctrineCriteriaQueryBuilderTest extends BaseIntegrationTestCase
{
    public ConditionFilter $likeConditionFilter;
    public ConditionFilter $lessThatConditionFilter;
    public ConditionFilter $startsConditionFilter;
    public CompositeFilter $compositeAndFilter;
    public CompositeFilter $compositeOrFilter;


    protected function setUp(): void
    {
        $this->likeConditionFilter     = FilterFactory::nlike('title', 'shazam');
        $this->lessThatConditionFilter = FilterFactory::lte('id', 3);
        $this->startsConditionFilter   = FilterFactory::starts('description', 'this is a description');
        $this->compositeAndFilter      = FilterFactory::and($this->likeConditionFilter, $this->lessThatConditionFilter);
        $this->compositeOrFilter       = FilterFactory::or($this->startsConditionFilter, $this->compositeAndFilter);

        parent::setUp();
    }

    /**
     * @return array<int, array<int, array<int,FilterVisitor>>>
     */
    public function dataProviderVisitors(): array
    {
        return [
            [
                [
                    new FilterExtensionVisitorFacade(new DateFilterExtensionVisitor()),
                    new FilterExtensionVisitorFacade(new TitleFilterExtensionVisitor()),
                ],
            ],
            [
                [
                    new DateFilterVisitor(),
                    new TitleFilterVisitor(),
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderVisitors
     *
     * @param array<FilterVisitor> $visitors
     * @return void
     */
    public function testItShouldBuildDoctrineQueryWithCustomVisitors(array $visitors): void
    {
        $offsetPagination   = new OffsetPagination(1, 100);
        $orderBy            = new OrderBy('createdAt', OrderBy::DESC);
        $compositeAndFilter = FilterFactory::and(
            FilterFactory::nlike('title', 'shazam21'),
            $this->lessThatConditionFilter
        );
        $dateFilter         = FilterFactory::eq('createdAt', '2023-01-10');
        $filters            = FilterFactory::or($dateFilter, $compositeAndFilter);
        $pageCriteria       = new PageCriteria(
            [$filters],
            $offsetPagination,
            $orderBy
        );
        $queryBuilder       = self::getDoctrineManager()
            ->createQueryBuilder()
            ->select('p.id, p.title, p.createdAt')
            ->from(Page::class, 'p');


        $visitorCollection            = new VisitorCollection([
            Driver::DOCTRINE_ORM->value => $visitors,
        ]);
        $doctrineCriteriaQueryBuilder = new DoctrineCriteriaBuilder(
            $queryBuilder,
            $pageCriteria,
            $visitorCollection->getVisitorsByDriver(Driver::DOCTRINE_ORM->value)
        );
        $doctrineQuery = $doctrineCriteriaQueryBuilder->where()->getQuery();

        $expectedDQL = \sprintf(
            'SELECT p.id, p.title, p.createdAt FROM %s p WHERE '.
            '(p.createdAt >= %s or (p.title = %s and p.id <= %s))',
            Page::class,
            ...\array_keys($filters->expression()->getParameters())
        );

        $this->assertCount(3, $filters->expression()->getParameters());
        $this->assertSame($expectedDQL, $doctrineQuery->getDQL());
        $this->assertNotNull($doctrineQuery->getResult());
    }


    public function testItShouldBuildDoctrineQueryWithDefaultVisitors(): void
    {
        $offsetPagination = new OffsetPagination(1, 30);
        $orderBy          = new OrderBy('createdAt', OrderBy::DESC);
        $pageCriteria     = new PageCriteria([$this->compositeOrFilter], $offsetPagination, $orderBy);
        $queryBuilder     = self::getDoctrineManager()
            ->createQueryBuilder()
            ->select('p.id, p.title')
            ->from(Page::class, 'p');

        // Assert the where and parameters with DQL
        $doctrineCriteriaQueryBuilder = new DoctrineCriteriaBuilder(
            $queryBuilder,
            $pageCriteria
        );
        $doctrineQuery = $doctrineCriteriaQueryBuilder
            ->where()
            ->getQuery();

        $expectedDQL = \sprintf(
            'SELECT p.id, p.title FROM %s p WHERE (p.description like %s or (p.title not like %s and p.id <= %s))',
            Page::class,
            ...\array_keys($this->compositeOrFilter->expression()->getParameters())
        );

        $this->assertCount(3, $this->compositeOrFilter->expression()->getParameters());
        $this->assertSame($expectedDQL, $doctrineQuery->getDQL());
        $this->assertNotNull($doctrineQuery->getResult());

        // Assert the where, limit and order with SQL, since DQL does not support Limit clause
        $doctrineQueryWithLimitAndOrder = $doctrineCriteriaQueryBuilder
            ->where()
            ->withLimit()
            ->withOrder()
            ->getQuery();

        $this->assertSame(
            'SELECT p0_.id AS id_0, p0_.title AS title_1 FROM page p0_'.
            ' WHERE (p0_.description LIKE ? OR (p0_.title NOT LIKE ? AND p0_.id <= ?))'.
            ' ORDER BY p0_.created_at DESC LIMIT 30',
            $doctrineQueryWithLimitAndOrder->getSQL()
        );
        $this->assertNotNull($doctrineQuery->getResult());
    }

}
