<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Filter;

use Ranky\SharedBundle\Filter\Visitor\FilterVisitor;

class ConditionFilter implements Filter
{
    private string $field;
    private ConditionOperator $operator;
    private mixed $value;
    private Expression $expression;

    public function __construct(string $field, ConditionOperator $operator, mixed $value)
    {
        $this->field       = $field;
        $this->operator    = $operator;
        $this->value       = $value;
        $this->expression  = $this->createDefaultExpression();
    }

    public function field(): string
    {
        return $this->field;
    }

    public function operator(): ConditionOperator
    {
        return $this->operator;
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function expression(): Expression
    {
        return $this->expression;
    }

    public function setExpression(Expression $expression): void
    {
        $this->expression = $expression;
    }

    public function setField(string $field): void
    {
        $this->field = $field;
    }

    public function setOperator(ConditionOperator $operator): void
    {
        $this->operator = $operator;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function fieldToKeyParameter(): string
    {
        $parameter = \str_replace('.', '_', \trim($this->field));
        return ':'.$parameter.'_'.\mt_rand();
    }

    public function accept(FilterVisitor $filterVisitor, Criteria $criteria): void
    {
        if ($filterVisitor->support($this, $criteria)){
            $filterVisitor->visitConditionFilter($this, $criteria);
        }
    }

    private function createDefaultExpression(): Expression
    {
        $parameters = [
            $this->fieldToKeyParameter() => $this->value,
        ];

        return new Expression(
            \sprintf(
                '%s %s %s',
                $this->field(),
                $this->operator()->expression(),
                $this->value,
            ),
            $parameters
        );
    }
}
