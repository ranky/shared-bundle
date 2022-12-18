<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\QueryParser;

use Ranky\SharedBundle\Filter\QueryParser\AST\Node\CompositeOperatorNode;
use Ranky\SharedBundle\Filter\QueryParser\AST\Visitor\NormalizeNodeAttributesVisitor;
use Ranky\SharedBundle\Filter\QueryParser\AST\Visitor\VisitorNode;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\WhereCloseGroupNode;
use Ranky\SharedBundle\Filter\QueryParser\Exception\NodeException;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\FunctionNode;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\Node;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\WhereOpenGroupNode;

/**
 * Generate Abstract Syntax Tree, ignoring what doesn't apply
 * @implements \IteratorAggregate<int, Node>
 */
class AST implements \IteratorAggregate
{
    /** @var TokenStream<Token> */
    public TokenStream $tokenStream;
    /** @var \ArrayIterator<int,Node> */
    public \ArrayIterator $ast;
    /** @var array<VisitorNode> */
    public array $visitors = [];

    /**
     * @param array<VisitorNode> $visitors
     */
    public function __construct(TokenStream $tokenStream, array $visitors = [])
    {
        $this->ast         = new \ArrayIterator();
        $this->tokenStream = $tokenStream;
        $this->visitors    = [
            new NormalizeNodeAttributesVisitor(),
            ...$visitors,
        ];
    }

    public function addVisitor(VisitorNode $visitorNode): void
    {
        $this->visitors[] = $visitorNode;
    }

    /**
     * @return \ArrayIterator<int, Node>
     */
    public function getIterator(): \ArrayIterator
    {
        return $this->ast;
    }

    /**
     * @return \ArrayIterator<int,Node>
     */
    public function getAST(): \ArrayIterator
    {
        while ($this->tokenStream->current()) {
            if ($node = $this->getNodeType($this->tokenStream->current())) {
                $this->ast->append($node);
                foreach ($this->visitors as $visitor) {
                    $node->accept($visitor, $this->ast);
                }
            }
            $this->tokenStream->next();
        }

        return $this->ast;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function toArray(): array
    {
        if (!$this->ast->count()) {
            $this->ast = $this->getAST();
        }
        $astArray = [];
        foreach ($this->ast as $ast) {
            $astArray[] = $ast->toArray();
        }

        return $astArray;
    }

    private function getNodeType(Token $token): ?Node
    {
        switch ($token->type()) {
            case (TokenGrammar::OPEN_GROUP_PARENTHESIS):
            {
                $node = new WhereOpenGroupNode($token);
                $this->tokenStream->next();
                while ($this->tokenStream->current()) {
                    if ($this->tokenStream->current()->type() === TokenGrammar::CLOSE_GROUP_PARENTHESIS) {
                        $this->tokenStream->seek($this->tokenStream->key()-1);
                        break;
                    }
                    if ($this->tokenStream->isLast()) {
                        throw new NodeException('Closing parenthesis not found when constructing the AST');
                    }
                    $childNode = $this->getNodeType($this->tokenStream->current());
                    if ($childNode !== null) {
                        $node->addNode($childNode);
                    }
                    $this->tokenStream->next();
                }

                return $node;
            }
            case (TokenGrammar::CLOSE_GROUP_PARENTHESIS):
            {
                return new WhereCloseGroupNode($token);
            }
            case (TokenGrammar::OPERATOR):
            {

                $node = new CompositeOperatorNode($token);
                $node->addAttribute('operator', \mb_strtolower($token->value()));

                return $node;
            }
            case (TokenGrammar::FUNCTION):
            {
                \preg_match('/(?<name>\w+)\((?<field>[^()]+),(?<value>[^()]+)\)/', $token->value(), $matches);
                $attributes = \array_filter($matches, "\is_string", \ARRAY_FILTER_USE_KEY);

                if (!isset($attributes['name'], $attributes['field'], $attributes['value'])) {
                    throw new NodeException(
                        'The function node requires the attributes "name, field and value". Some of them are missing.'
                    );
                }

                $node = new FunctionNode($token);
                $node->setAttributes($attributes);

                return $node;
            }
            default: {
                return null;
            }

        }
    }
}
