<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\QueryParser;

use Ranky\SharedBundle\Filter\CompositeOperator;

enum TokenGrammar: int
{
    case OPEN_GROUP_PARENTHESIS = 1;
    case CLOSE_GROUP_PARENTHESIS = 2;
    case OPERATOR = 3;
    case FUNCTION = 4;
    case SPACE = 5;
    case NONE = 6;

    public function regex(): string
    {
        $logicOperators = \implode('|', CompositeOperator::operators());

        return match ($this) {
            self::NONE => '',
            self::OPEN_GROUP_PARENTHESIS => '/(?<open_group_parenthesis>\((?=\w))/',
            self::CLOSE_GROUP_PARENTHESIS => '/(?<close_group_parenthesis>(?<=\))\)(?=\s))/',
            self::OPERATOR => \sprintf('/(?:\s+)?(?<operator>%s)(?:\s+)?/', $logicOperators),
            self::FUNCTION => '/(?:\s+)?(?<function>(?:\w+)\([^()]+,[^()]+\))(?:\s+)?/',
            self::SPACE => '/(?<space>\s)/',// \T_WHITESPACE === ord($token)
        };
    }

    /**
     * @return array<string>
     */
    public static function splitRegexPatterns(): array
    {
        return \array_map(static fn(string $regex) => \trim($regex, '/'), [
            self::OPEN_GROUP_PARENTHESIS->regex(),
            self::CLOSE_GROUP_PARENTHESIS->regex(),
            self::OPERATOR->regex(),
            self::FUNCTION->regex(),
        ]);
    }

    /**
     * @return array<self>
     */
    public static function allowStartTokens(): array
    {
        return [self::OPEN_GROUP_PARENTHESIS, self::FUNCTION];
    }

    /**
     * @return array<self>
     */
    public static function allowEndTokens(): array
    {
        return [self::OPEN_GROUP_PARENTHESIS, self::FUNCTION];
    }

    /**
     * @return array<string>
     */
    public static function tokens(): array
    {
        return \array_map(static fn(self $token) => $token->name, self::cases());
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
