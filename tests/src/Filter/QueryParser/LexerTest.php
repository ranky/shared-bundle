<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Filter\QueryParser;

use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Filter\ConditionOperator;
use Ranky\SharedBundle\Filter\QueryParser\Exception\TokenException;
use Ranky\SharedBundle\Filter\QueryParser\Lexer;
use Ranky\SharedBundle\Filter\QueryParser\Token;
use Ranky\SharedBundle\Filter\QueryParser\TokenGrammar;

class LexerTest extends TestCase
{

    public function testItShouldCheckValidTokenPosition(): void
    {
        $input       = "(eq('title','bar') or eq(title,'title')) and eq('title','baz')";
        $lexer       = new Lexer($input);
        $tokenStream = $lexer->tokenize();
        /** @var Token $token */
        foreach ($tokenStream as $token) {
            $actual = \mb_substr($input, $token->startPosition(), $token->endPosition() - $token->startPosition());
            $this->assertSame($token->value(), $actual);
        }
    }

    public function testItShouldGetAValidTokenStream(): void
    {
        $lexer = new Lexer("(eq('title','bar') or eq(title,'title')) and eq('title','baz')");

        $this->assertEquals(
            [
                new Token('(', TokenGrammar::OPEN_GROUP_PARENTHESIS, 0, 1),
                new Token("eq('title','bar')", TokenGrammar::FUNCTION, 1, 18),
                new Token('or', TokenGrammar::OPERATOR, 19, 21),
                new Token("eq(title,'title')", TokenGrammar::FUNCTION, 22, 39),
                new Token(')', TokenGrammar::CLOSE_GROUP_PARENTHESIS, 39, 40),
                new Token('and', TokenGrammar::OPERATOR, 41, 44),
                new Token("eq('title','baz')", TokenGrammar::FUNCTION, 45, 62),
            ],
            iterator_to_array($lexer->tokenize())
        );
    }

    public function testItShouldThrowExceptionWhenAClosingParenthesisIsNotFollowedByOperatorOrBeTheEndOfTheQuery(): void
    {
        $this->expectException(TokenException::class);
        $this->expectExceptionMessage(
            'Syntax error. A closing parenthesis must be followed by a '.
            'conditional operator or be the end of the query.'
        );
        $lexer = new Lexer("(eq('title','bar'))   eq('title','bar')");
        $lexer->tokenize();
    }

    public function testItShouldThrowExceptionWithNoOperatorBetweenFunctions(): void
    {
        $this->expectException(TokenException::class);
        $this->expectExceptionMessage(
            'Syntax error. A function must be followed by a '.
            'conditional operator or a closing parenthesis or be the end of the query.'
        );
        $lexer = new Lexer("eq('title','bar') eq('title','bar')");
        $lexer->tokenize();
    }

    public function testItShouldThrowExceptionWithAnUnknownOperator(): void
    {
        $this->expectException(TokenException::class);
        $this->expectExceptionMessage(
            'Syntax error. A function must be followed by a '.
            'conditional operator or a closing parenthesis or be the end of the query.'
        );
        $lexer = new Lexer("eq('title','bar') caca eq('title','bar')");
        $lexer->tokenize();
    }

    public function testItShouldThrowTokenExceptionWithNoCloseParenthesis(): void
    {
        $this->expectException(TokenException::class);
        $this->expectExceptionMessage('One or more parentheses have not been closed');
        $lexer = new Lexer("(eq('title','bar')");
        $lexer->tokenize();
    }

    public function testItShouldThrowTokenExceptionWithMultipleNoCloseParenthesis(): void
    {
        $this->expectException(TokenException::class);
        $this->expectExceptionMessage('One or more parentheses have not been closed');
        $lexer = new Lexer("(eq('title','bar') or (eq('title','bar')) or eq('title','bar')");
        $lexer->tokenize();
    }

    public function testItShouldThrowNodeExceptionWithNotValidFunction(): void
    {
        $this->expectException(TokenException::class);
        $this->expectExceptionMessage(
            \sprintf(
                'The "%s" function is not valid. List of valid functions: %s.',
                'hello',
                \implode(', ', ConditionOperator::operators())
            )
        );
        $lexer = new Lexer("eq('title','bar') or hello('title','bar')");
        $lexer->tokenize();
    }

}
