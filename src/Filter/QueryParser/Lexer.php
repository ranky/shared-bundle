<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\QueryParser;

use Ranky\SharedBundle\Filter\ConditionOperator;
use Ranky\SharedBundle\Filter\QueryParser\Exception\TokenException;

/**
 * Lexical analysis - Parsing token classifications
 * Takes code from the input and breaks it into smaller pieces.
 * It groups the input code into sequences of characters called lexemes, each of which corresponds to a TOKEN.
 */
class Lexer
{
    private string $input;
    private TokenStream $tokenStream;

    public function __construct(string $input)
    {
        $this->input       = $input;
        $this->tokenStream = new TokenStream([]);
    }

    /**
     * @return TokenStream<Token>
     */
    public function getTokenStream(): TokenStream
    {
        return $this->tokenStream;
    }

    /**
     * Tokenization | Lexing
     */
    public function tokenize(): TokenStream
    {
        if ($this->tokenStream->count()){
            return $this->tokenStream;
        }
        $streams             = $this->split();
        $isParenthesisClosed = true;
        foreach ($streams as $key => $stream) {
            [$value, $position] = $stream;
            $type = $this->getTokenType($value);
            if ($type === TokenGrammar::OPEN_GROUP_PARENTHESIS || $type === TokenGrammar::CLOSE_GROUP_PARENTHESIS) {
                $isParenthesisClosed = !$isParenthesisClosed;
            }
            if ($type === TokenGrammar::FUNCTION && isset($streams[$key + 1])) {
                $nextType = $this->getTokenType($streams[$key + 1][0]);
                if ($nextType !== TokenGrammar::OPERATOR &&
                    $nextType !== TokenGrammar::CLOSE_GROUP_PARENTHESIS) {
                    throw new TokenException(
                        'Syntax error. A function must be followed by a '.
                        'conditional operator or a closing parenthesis or be the end of the query.'
                    );
                }
            }
            if ($type === TokenGrammar::CLOSE_GROUP_PARENTHESIS && isset($streams[$key + 1])) {
                $nextType = $this->getTokenType($streams[$key + 1][0]);
                if ($nextType !== TokenGrammar::OPERATOR &&
                    $nextType !== TokenGrammar::CLOSE_GROUP_PARENTHESIS) {
                    throw new TokenException(
                        'Syntax error. A closing parenthesis must be followed by a '.
                        'conditional operator or be the end of the query.'
                    );
                }
            }
            $this->tokenStream->append(
                new Token(
                    $value,
                    $type,
                    $position,
                    $position + \mb_strlen($value)
                )
            );
        }
        // check start tokens
        $firstToken = $this->tokenStream->first();
        if ($firstToken && !\in_array(
            $firstToken->type(),
            TokenGrammar::allowStartTokens(),
            true
        )) {
            throw new TokenException(
                \sprintf(
                    'Token "%s" with "%s" value not allowed at start. Tokens allowed at the start are: %s',
                    $firstToken->type()->name,
                    $firstToken->value(),
                    \implode(
                        ', ',
                        \array_map(
                            static fn(TokenGrammar $token) => $token->name,
                            TokenGrammar::allowStartTokens()
                        )
                    )
                )
            );
        }

        // check end tokens
        $lastToken = $this->tokenStream->last();
        if ($lastToken && !\in_array(
            $lastToken->type(),
            TokenGrammar::allowEndTokens(),
            true
        )) {
            throw new TokenException(
                \sprintf(
                    'Token "%s" with "%s" value not allowed at end. Tokens allowed at the end are: %s',
                    $lastToken->type()->name,
                    $lastToken->value(),
                    \implode(
                        ', ',
                        \array_map(
                            static fn(TokenGrammar $token) => $token->name,
                            TokenGrammar::allowEndTokens()
                        )
                    )
                )
            );
        }
        if (!$isParenthesisClosed) {
            throw new TokenException('One or more parentheses have not been closed');
        }

        return $this->tokenStream;
    }

    /**
     * Split input into smaller pieces by regex
     *
     * @return array<int, array{string, int}>
     */
    private function split(): array
    {
        if (!$this->input) {
            return [];
        }
        $flags = \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_OFFSET_CAPTURE;
        $regex = \sprintf(
            '/%s/',
            \implode('|', TokenGrammar::splitRegexPatterns()),
        );

        return \preg_split($regex, \trim($this->input), -1, $flags) ?: [];
    }

    /**
     * Lexical analyzer o scanner
     * Assign each piece to its corresponding token,
     * if it does not exist by default the token will be assigned as NONE
     *
     * @param string $value
     * @return TokenGrammar
     */
    public function getTokenType(string &$value): TokenGrammar
    {
        // remove whitespaces
        $value = \trim($value);
        // make regex from TokenGrammar->regex()
        switch (true) {
            case ($value === '('):
            {
                return TokenGrammar::OPEN_GROUP_PARENTHESIS;
            }
            case ($value === ')'):
            {
                return TokenGrammar::CLOSE_GROUP_PARENTHESIS;
            }
            case (\preg_match(TokenGrammar::OPERATOR->regex(), $value)):
            {
                return TokenGrammar::OPERATOR;
            }
            case (\preg_match(TokenGrammar::FUNCTION->regex(), $value, $matches)):
            {
                $operators = ConditionOperator::operators();
                \preg_match(
                    '/(?<name>\w+)\((?<field>[^()]+),(?<value>[^()]+)\)/',
                    $matches['function'],
                    $matchesFunction
                );

                if (!\in_array($matchesFunction['name'], $operators, true)) {
                    throw new TokenException(
                        \sprintf(
                            'The "%s" function is not valid. List of valid functions: %s.',
                            $matchesFunction['name'],
                            \implode(', ', $operators)
                        )
                    );
                }

                return TokenGrammar::FUNCTION;
            }
            default:
            {
                return TokenGrammar::NONE;
            }
        }
    }

}
