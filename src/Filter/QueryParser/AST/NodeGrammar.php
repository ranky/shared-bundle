<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\QueryParser\AST;


enum NodeGrammar: int
{
    case WHERE_OPEN_GROUP = 1;
    case WHERE_CLOSE_GROUP = 2;
    case COMPOSITE_OPERATOR = 3;
    case FUNCTION = 4;

    /**
     * @return array<string>
     */
    public static function nodes(): array
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
