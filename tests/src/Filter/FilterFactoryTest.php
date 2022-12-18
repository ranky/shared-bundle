<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Filter\ConditionFilter;
use Ranky\SharedBundle\Filter\ConditionOperator;
use Ranky\SharedBundle\Filter\FilterFactory;

class FilterFactoryTest extends TestCase
{

    public function testItShouldCreateValidCompositeFilterWithAdditionalComposite(): void
    {
        $likeConditionFilter     = FilterFactory::like('title', 'title');
        $lessThatConditionFilter = FilterFactory::lt('id', 2);
        $compositeAndFilter      = FilterFactory::and($likeConditionFilter, $lessThatConditionFilter);

        $startsConditionFilter = FilterFactory::starts('description', 'description');
        $compositeFilter       = FilterFactory::or($startsConditionFilter, $compositeAndFilter);

        $this->assertCount(2, $compositeFilter);
        $conditionFilters = $compositeFilter->getIterator();
        /** @var \ArrayIterator<int,ConditionFilter> $conditionFilters */
        $this->assertSame($conditionFilters->current()->value(), $startsConditionFilter->value());
        $this->assertSame($conditionFilters->current()->field(), $startsConditionFilter->field());
        $this->assertSame($conditionFilters->current()->operator(), $startsConditionFilter->operator());
        $conditionFilters->next();
        //dd($conditionFilters->current());
        $this->assertSame($conditionFilters->current(), $compositeAndFilter);
    }

    public function testItShouldCreateValidCompositeFilter(): void
    {
        $equalsConditionFilter   = FilterFactory::eq('title', 'title');
        $noEqualsConditionFilter = FilterFactory::neq('title', 'ntitle');

        $compositeFilter = FilterFactory::and($equalsConditionFilter, $noEqualsConditionFilter);
        $this->assertCount(2, $compositeFilter);
        $conditionFilters = $compositeFilter->getIterator();
        /** @var \ArrayIterator<int,ConditionFilter> $conditionFilters */
        $this->assertSame($conditionFilters->current()->value(), $equalsConditionFilter->value());
        $this->assertSame($conditionFilters->current()->field(), $equalsConditionFilter->field());
        $this->assertSame($conditionFilters->current()->operator(), $equalsConditionFilter->operator());

        $conditionFilters->next();

        $this->assertSame($conditionFilters->current()->value(), $noEqualsConditionFilter->value());
        $this->assertSame($conditionFilters->current()->field(), $noEqualsConditionFilter->field());
        $this->assertSame($conditionFilters->current()->operator(), $noEqualsConditionFilter->operator());

    }

    public function testItShouldCreateEqualsConditionFilterWithValidPlaceholder(): void
    {
        $eqConditionFilter = FilterFactory::eq('title', 'title');

        $this->assertSame('title', $eqConditionFilter->field());
        $this->assertSame(ConditionOperator::EQUALS, $eqConditionFilter->operator());
        $this->assertSame('title', $eqConditionFilter->value());
        $placeholder = \str_replace('.', '_', \trim($eqConditionFilter->field()));
        $this->assertMatchesRegularExpression('/^:'.$placeholder.'_[0-9]+/', $eqConditionFilter->fieldToKeyParameter());
    }

    /**
     * @dataProvider dataProviderConditionFunctions
     */
    public function testItShouldValidConditionFilters(
        ConditionOperator $conditionOperator,
        string $field,
        mixed $value
    ): void {
        $conditionFilter = FilterFactory::{$conditionOperator->value}($field, $value);

        $this->assertSame($field, $conditionFilter->field());
        $this->assertSame($conditionOperator, $conditionFilter->operator());
        $this->assertSame($value, $conditionFilter->value());
    }

    /**
     * @return array<array{ConditionOperator, string, string}>
     */
    public function dataProviderConditionFunctions(): array
    {
        return [
            [ConditionOperator::NOT_EQUALS, 'field', 'value'],
            [ConditionOperator::STARTS, 'field', 'value'],
            [ConditionOperator::ENDS, 'field', 'value'],
            [ConditionOperator::GREATER_THAN, 'field', 'value'],
            [ConditionOperator::GREATER_THAN_OR_EQUAL, 'field', 'value'],
            [ConditionOperator::LESS_THAN, 'field', 'value'],
            [ConditionOperator::LESS_THAN_OR_EQUAL, 'field', 'value'],
            [ConditionOperator::LIKE, 'field', 'value'],
            [ConditionOperator::NOT_LIKE, 'field', 'value'],
            [ConditionOperator::INCLUDE, 'field', 'value'],
            [ConditionOperator::NOT_INCLUDE, 'field', 'value'],
        ];
    }
}
