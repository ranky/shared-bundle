<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Filter;

enum ConditionOperator: string
{
    public const DEFAULT_OPERATOR = 'eq';

    case EQUALS = 'eq';
    case NOT_EQUALS = 'neq';
    case GREATER_THAN = 'gt';
    case GREATER_THAN_OR_EQUAL = 'gte';
    case LESS_THAN = 'lt';
    case LESS_THAN_OR_EQUAL = 'lte';
    case LIKE = 'like';
    case NOT_LIKE = 'nlike';
    case INCLUDE = 'in';
    case NOT_INCLUDE = 'nin';
    case STARTS = 'starts';
    case ENDS = 'ends';



    public function expression(): string
    {
        return match ($this) {
            self::EQUALS => '=',
            self::NOT_EQUALS => '!=',
            self::GREATER_THAN => '>',
            self::GREATER_THAN_OR_EQUAL => '>=',
            self::LESS_THAN => '<',
            self::LESS_THAN_OR_EQUAL => '<=',
            self::LIKE,
            self::STARTS,
            self::ENDS => 'like',
            self::NOT_LIKE => 'not like',
            self::INCLUDE => 'in',
            self::NOT_INCLUDE=> 'not in',
        };
    }

    /**
     * @return array<string>
     */
    public static function operators(): array
    {
        return \array_map(static fn(self $operator) => $operator->value, self::cases());
    }

    public static function tryFromName(string $name): ?self
    {
        try {
            return \constant("self::$name");
        } catch (\Throwable) {
            return null;
        }
    }

}
