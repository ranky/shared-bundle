<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Filter;

class FilterFactory
{

    public static function and(Filter ...$filters): CompositeFilter
    {
        return new CompositeFilter(CompositeOperator::AND, ...$filters);
    }

    public static function or(Filter ...$filters): CompositeFilter
    {
        return new CompositeFilter(CompositeOperator::OR, ...$filters);
    }

    public static function create(string $field, ConditionOperator $filterOperator, mixed $value): ConditionFilter
    {
        return new ConditionFilter($field, $filterOperator, $value);
    }

    public static function eq(string $field, mixed $value): ConditionFilter
    {
        return new ConditionFilter($field, ConditionOperator::EQUALS, $value);
    }

    public static function neq(string $field, mixed $value): ConditionFilter
    {
        return new ConditionFilter($field, ConditionOperator::NOT_EQUALS, $value);
    }

    public static function gt(string $field, mixed $value): ConditionFilter
    {
        return new ConditionFilter($field, ConditionOperator::GREATER_THAN, $value);
    }

    public static function gte(string $field, mixed $value): ConditionFilter
    {
        return new ConditionFilter($field, ConditionOperator::GREATER_THAN_OR_EQUAL, $value);
    }

    public static function lt(string $field, mixed $value): ConditionFilter
    {
        return new ConditionFilter($field, ConditionOperator::LESS_THAN, $value);
    }

    public static function lte(string $field, mixed $value): ConditionFilter
    {
        return new ConditionFilter($field, ConditionOperator::LESS_THAN_OR_EQUAL, $value);
    }

    public static function like(string $field, mixed $value): ConditionFilter
    {
        return new ConditionFilter($field, ConditionOperator::LIKE, $value);
    }

    public static function nlike(string $field, mixed $value): ConditionFilter
    {
        return new ConditionFilter($field, ConditionOperator::NOT_LIKE, $value);
    }

    public static function starts(string $field, mixed $value): ConditionFilter
    {
        return new ConditionFilter($field, ConditionOperator::STARTS, $value);
    }

    public static function ends(string $field, mixed $value): ConditionFilter
    {
        return new ConditionFilter($field, ConditionOperator::ENDS, $value);
    }

    public static function in(string $field, mixed $value): ConditionFilter
    {
        return new ConditionFilter($field, ConditionOperator::INCLUDE, $value);
    }
    public static function nin(string $field, mixed $value): ConditionFilter
    {
        return new ConditionFilter($field, ConditionOperator::NOT_INCLUDE, $value);
    }
}
