<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter;

class FilterHelper
{
    /**
     * @param Filter $filter
     * @param array<string> $expressionList
     * @return array<string>
     */
    public static function updateExpressionTree(Filter $filter, array &$expressionList = []): array
    {
        if ($filter instanceof ConditionFilter) {
            $expressionList[] = $filter->expression()->getExpression();

            return $expressionList;
        }
        if ($filter instanceof CompositeFilter) {
            foreach ($filter->filters() as $childFilter) {
                if ($childFilter instanceof CompositeFilter) {
                    $childExpressionsList = \array_map(
                        static fn(Filter $filter) => $filter->expression()->getExpression(),
                        $childFilter->filters()
                    );
                    $expression  = \sprintf(
                        '(%s)',
                        \implode(' '.$filter->operator()->expression().' ', $childExpressionsList)
                    );
                    $childFilter->expression()->setExpression($expression);
                    $expressionList = [...$expressionList, $expression];
                } else {
                    $expressionList = [...$expressionList, $childFilter->expression()->getExpression()];
                }
            }
        }
        $filter->expression()->setExpression(
            \sprintf(
                '(%s)',
                \implode(' '.$filter->operator()->expression().' ', $expressionList)
            )
        );

        return $expressionList;
    }

    /**
     * @param Filter $filter
     * @return string[]
     */
    public static function getParameterTree(Filter $filter): array
    {
        $parameters = [];
        if ($filter instanceof ConditionFilter) {
            $parameters[$filter->fieldToKeyParameter()] = $filter->value();

            return $parameters;
        }
        if ($filter instanceof CompositeFilter) {
            foreach ($filter->filters() as $filterRecursive) {
                if ($filterRecursive instanceof ConditionFilter) {
                    $parameters[$filterRecursive->fieldToKeyParameter()] = $filterRecursive->value();
                } else {
                    $parameters = [...$parameters, ...self::getParameterTree($filterRecursive)];
                }
            }
        }

        return $parameters;
    }

    public static function castValue(mixed $value): mixed
    {
        if (\filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE)) {
            $value = (float)$value;
        }
        if (\filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE)) {
            $value = (int)$value;
        }
        if ($value === 'true' || $value === 'false'){
            $value = (bool)$value;
        }
        if ($value === 'null'){
            $value = null;
        }
        return $value;
    }
}
