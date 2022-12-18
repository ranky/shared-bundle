<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Common\ClassHelper;
use Ranky\SharedBundle\Filter\Exception\CriteriaException;
use Ranky\SharedBundle\Filter\Exception\FilterException;
use Ranky\SharedBundle\Filter\ConditionOperator;
use Ranky\SharedBundle\Filter\Order\OrderBy;
use Ranky\SharedBundle\Filter\Pagination\OffsetPagination;
use Ranky\SharedBundle\Filter\ConditionFilter;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\PageCriteria;

class CriteriaTest extends TestCase
{

    public function testItShouldInitializeCriteriaFromRequestWithDefaultValues(): void
    {
        $pageCriteria = PageCriteria::fromRequest([], [], []);

        $this->assertSame([], $pageCriteria->filters());
        $this->assertSame(PageCriteria::DEFAULT_PAGINATION_LIMIT, $pageCriteria->offsetPagination()->limit());
        $this->assertSame($pageCriteria::normalizeField(PageCriteria::DEFAULT_ORDER_FIELD), $pageCriteria->orderBy()->field());
        $this->assertSame(PageCriteria::DEFAULT_ORDER_DIRECTION, $pageCriteria->orderBy()->direction());
    }

    public function testItShouldInitializeCriteriaFromRequestWithCustomPaginationAndOrder(): void
    {
        $pageCriteria = PageCriteria::fromRequest(
            [],
            ['limit' => 10, 'number' => 2],
            ['field' => 'updatedAt', 'direction' => 'ASC']
        );

        $this->assertSame([], $pageCriteria->filters());
        $this->assertSame(10, $pageCriteria->offsetPagination()->limit());
        $this->assertSame(2, $pageCriteria->offsetPagination()->page());
        $this->assertSame($pageCriteria::normalizeField('updatedAt'), $pageCriteria->orderBy()->field());
        $this->assertSame('ASC', $pageCriteria->orderBy()->direction());
    }

    public function testItShouldInitializeCriteriaFromRequestWithFilters(): void
    {
        $filters = [
            new ConditionFilter('id', ConditionOperator::EQUALS, 1),
            new ConditionFilter('title', ConditionOperator::LIKE, 'title'),
            new ConditionFilter('description', ConditionOperator::EQUALS, 'description'),
        ];
        $filtersFromRequest = [
            'id' => [
                'eq' => 1,
            ],
            'title' => [
                'like' => 'title',
            ],
            'description' => 'description',
        ];
        $pageCriteria = PageCriteria::fromRequest($filtersFromRequest);
        $pageCriteriaFilters = $pageCriteria->filters();
        /** @var array<ConditionFilter> $pageCriteriaFilters */


        $this->assertSame(PageCriteria::MODEL_ALIAS.'.'.$filters[0]->field(), $pageCriteriaFilters[0]->field());
        $this->assertSame(PageCriteria::MODEL_ALIAS.'.'.$filters[1]->field(), $pageCriteriaFilters[1]->field());
        $this->assertSame(PageCriteria::MODEL_ALIAS.'.'.$filters[2]->field(), $pageCriteriaFilters[2]->field());
        $this->assertEquals($filters[0]->operator(), $pageCriteria->filters()[0]->operator());
        $this->assertEquals($filters[1]->operator(), $pageCriteria->filters()[1]->operator());
        $this->assertEquals($filters[2]->operator(), $pageCriteria->filters()[2]->operator());
        $this->assertSame($filters[0]->value(), $pageCriteriaFilters[0]->value());
        $this->assertSame($filters[1]->value(), $pageCriteriaFilters[1]->value());
        $this->assertSame($filters[2]->value(), $pageCriteriaFilters[2]->value());
    }

    public function testItShouldThrowExceptionWithNotExistNormalizeValue(): void
    {
        $this->expectExceptionObject(
            CriteriaException::notValidField('customField', ClassHelper::className(PageCriteria::class))
        );
        PageCriteria::fromRequest(
            [],
            ['limit' => 10, 'number' => 2],
            ['field' => 'customField', 'direction' => 'ASC']
        );
    }

    public function testItShouldThrowExceptionWithNoFilterInstance(): void
    {
        $this->expectException(FilterException::class);
        $offsetPagination = new OffsetPagination(1, 20);
        $orderBy          = new OrderBy('createdAt', 'DESC');
        /** @phpstan-ignore-next-line */
        $pageCriteria     = new PageCriteria([new \stdClass()], $offsetPagination, $orderBy);
    }

    public function testItShouldCreateValidPageCriteriaWithFilters(): void
    {
        $offsetPagination = new OffsetPagination(1, 20);
        $orderBy          = new OrderBy('createdAt', 'DESC');
        $filters          = [
            new ConditionFilter('id', ConditionOperator::EQUALS, 1),
            new ConditionFilter('title', ConditionOperator::LIKE, 'title'),
        ];
        $pageCriteria     = new PageCriteria($filters, $offsetPagination, $orderBy);

        $this->assertSame($filters, $pageCriteria->filters());
    }

}
