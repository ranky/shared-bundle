<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Filter;

enum CompositeOperator: string
{
    public const DEFAULT_OPERATOR = 'and';

    case AND = 'and';
    case OR = 'or';


    public function expression(): string
    {
        return match ($this) {
            self::AND => 'and',
            self::OR => 'or'
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
