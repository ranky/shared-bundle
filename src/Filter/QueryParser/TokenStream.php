<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\QueryParser;

/**
 * @extends \ArrayIterator<int, Token>
 */
class TokenStream extends \ArrayIterator
{

    /**
     * @param array<Token> $tokens
     */
    public function __construct(array $tokens)
    {
        parent::__construct($tokens);
    }

    public function isLast(): bool
    {
        return $this->key() === ($this->count() - 1);
    }

    public function lookahead(): ?Token
    {
        return $this->offsetGet($this->key() + 1);
    }

    public function lookbehind(): ?Token
    {
        return $this->offsetGet($this->key() - 1);
    }

    public function last(): ?Token
    {
        return $this->offsetGet($this->count() - 1);
    }

    public function first(): ?Token
    {
        return $this->offsetGet(0);
    }
}
