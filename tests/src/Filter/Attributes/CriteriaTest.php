<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Filter\Attributes;

use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Filter\Attributes\Criteria;
use Ranky\SharedBundle\Filter\Attributes\CriteriaValueResolver;
use Ranky\SharedBundle\Filter\ConditionOperator;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\PageCriteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;

class CriteriaTest extends TestCase
{
    public function testItShouldResolveControllerCriteriaAttributeWithQueryFilters(): void
    {
        $request = Request::create(
            uri: '/?filters[id][eq]=1&filters[title][like]=title&filters[description]=description'
        );

        $factory    = new ArgumentMetadataFactory();
        $controller = [new self(), 'controllerWithCriteriaAttribute'];

        $criteriaValueResolver = new CriteriaValueResolver(null);
        $resolver              = new ArgumentResolver($factory, [$criteriaValueResolver]);
        $arguments             = $resolver->getArguments($request, $controller);
        /** @var PageCriteria $pageCriteria */
        $pageCriteria = $arguments[0];

        $this->assertInstanceOf(PageCriteria::class, $pageCriteria);
        $this->assertCount(3, $pageCriteria->filters());
        $this->assertSame(ConditionOperator::EQUALS->name, $pageCriteria->filters()[0]->operator()->name);
        $this->assertSame(ConditionOperator::LIKE->name, $pageCriteria->filters()[1]->operator()->name);
        $this->assertSame(ConditionOperator::EQUALS->name, $pageCriteria->filters()[2]->operator()->name);
        $this->assertSame(PageCriteria::DEFAULT_PAGINATION_LIMIT, $pageCriteria->offsetPagination()->limit());
    }

    public function testItShouldResolveControllerCriteriaAttributeWithConstructorPaginationLimit(): void
    {
        $request = Request::create(uri: '/');

        $factory    = new ArgumentMetadataFactory();
        $controller = [new self(), 'controllerWithCriteriaAttribute'];

        $criteriaValueResolver = new CriteriaValueResolver(50);
        $resolver              = new ArgumentResolver($factory, [$criteriaValueResolver]);
        $arguments             = $resolver->getArguments($request, $controller);
        /** @var PageCriteria $pageCriteria */
        $pageCriteria = $arguments[0];

        $this->assertInstanceOf(PageCriteria::class, $pageCriteria);
        $this->assertCount(0, $pageCriteria->filters());
        $this->assertSame(50, $pageCriteria->offsetPagination()->limit());
    }

    public function testItShouldResolveControllerCriteriaAttributeWithAttributePaginationLimit(): void
    {
        $request    = Request::create(uri: '/');
        $factory    = new ArgumentMetadataFactory();
        $controller = [new self(), 'controllerWithCriteriaAttributeAndPaginationLimit'];

        $criteriaValueResolver = new CriteriaValueResolver(50);
        $resolver              = new ArgumentResolver($factory, [$criteriaValueResolver]);
        $arguments             = $resolver->getArguments($request, $controller);
        /** @var PageCriteria $pageCriteria */
        $pageCriteria = $arguments[0];

        $this->assertInstanceOf(PageCriteria::class, $pageCriteria);
        $this->assertCount(0, $pageCriteria->filters());
        $this->assertSame(100, $pageCriteria->offsetPagination()->limit());
    }

    public function testItShouldThrowExceptionWhenResolveControllerCriteriaAttributeWithDifferentClass(): void
    {
        $request    = Request::create(uri: '/');
        $factory    = new ArgumentMetadataFactory();
        $controller = [new self(), 'controllerWithCriteriaAttributeAndDifferentClass'];

        $this->expectException(\RuntimeException::class);
        $criteriaValueResolver = new CriteriaValueResolver(null);
        $resolver              = new ArgumentResolver($factory, [$criteriaValueResolver]);
        $resolver->getArguments($request, $controller);

    }

    public function controllerWithCriteriaAttribute(#[Criteria] PageCriteria $pageCriteria): void
    {
    }

    public function controllerWithCriteriaAttributeAndDifferentClass(#[Criteria] CriteriaTest $pageCriteria): void
    {
    }

    public function controllerWithCriteriaAttributeAndPaginationLimit(
        #[Criteria(paginationLimit: 100)]
        PageCriteria $pageCriteria
    ): void {
    }

}
