<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Filter\QueryParser;

use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Filter\QueryParser\AST;
use Ranky\SharedBundle\Filter\QueryParser\AST\NodeGrammar;
use Ranky\SharedBundle\Filter\QueryParser\Lexer;
use Ranky\SharedBundle\Filter\QueryParser\TokenGrammar;

class ASTTest extends TestCase
{

    public function testItShouldConvertQueryToValidAST(): void
    {
        $lexer = new Lexer("(eq('title','bar') or eq('title','baz')) or like('title','baz@gmail.com')");
        $ast   = new AST($lexer->tokenize());

        $this->assertSame([
            [
                'type' => NodeGrammar::WHERE_OPEN_GROUP->name,
                'token' => [
                    'value' => '(',
                    'type' => TokenGrammar::OPEN_GROUP_PARENTHESIS->name,
                    'startPosition' => 0,
                    'endPosition' => 1,
                ],
                'attributes' => [],
                'nodes' => [
                    [
                        'type' => NodeGrammar::FUNCTION->name,
                        'token' => [
                            'value' => "eq('title','bar')",
                            'type' => TokenGrammar::FUNCTION->name,
                            'startPosition' => 1,
                            'endPosition' => 18,
                        ],
                        'attributes' => [
                            'name' => 'EQUALS',
                            'field' => 'title',
                            'value' => 'bar',
                        ],
                        'nodes' => [],
                    ],
                    [
                        'type' => NodeGrammar::COMPOSITE_OPERATOR->name,
                        'token' => [
                            'value' => 'or',
                            'type' => TokenGrammar::OPERATOR->name,
                            'startPosition' => 19,
                            'endPosition' => 21,
                        ],
                        'attributes' => ['operator' => 'OR'],
                        'nodes' => [],
                    ],
                    [
                        'type' => NodeGrammar::FUNCTION->name,
                        'token' => [
                            'value' => "eq('title','baz')",
                            'type' => TokenGrammar::FUNCTION->name,
                            'startPosition' => 22,
                            'endPosition' => 39,
                        ],
                        'attributes' => [
                            'name' => 'EQUALS',
                            'field' => 'title',
                            'value' => 'baz',
                        ],
                        'nodes' => [],
                    ],
                ],
            ],
            [
                'type' => NodeGrammar::WHERE_CLOSE_GROUP->name,
                'token' => [
                    'value' => ')',
                    'type' => TokenGrammar::CLOSE_GROUP_PARENTHESIS->name,
                    'startPosition' => 39,
                    'endPosition' => 40,
                ],
                'attributes' => [],
                'nodes' => [],
            ],
            [
                'type' => NodeGrammar::COMPOSITE_OPERATOR->name,
                'token' => [
                    'value' => 'or',
                    'type' => TokenGrammar::OPERATOR->name,
                    'startPosition' => 41,
                    'endPosition' => 43,
                ],
                'attributes' => ['operator' => 'OR'],
                'nodes' => [],
            ],
            [
                'type' => NodeGrammar::FUNCTION->name,
                'token' => [
                    'value' => "like('title','baz@gmail.com')",
                    'type' => TokenGrammar::FUNCTION->name,
                    'startPosition' => 44,
                    'endPosition' => 73,
                ],
                'attributes' => [
                    'name' => 'LIKE',
                    'field' => 'title',
                    'value' => 'baz@gmail.com',
                ],
                'nodes' => [],
            ],
        ], $ast->toArray());
    }

}
