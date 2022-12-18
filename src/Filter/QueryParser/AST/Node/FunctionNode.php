<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\QueryParser\AST\Node;


use Ranky\SharedBundle\Filter\QueryParser\AST\NodeGrammar;
use Ranky\SharedBundle\Filter\QueryParser\AST\Visitor\VisitorNode;

/**
 * @property array{name: \Ranky\SharedBundle\Filter\ConditionOperator, field: string, value: mixed} $attributes
 */
class FunctionNode extends Node
{
    /**
     * @param VisitorNode $visitorNode
     * @param \ArrayIterator<int, Node> $nodes
     * @return void
     */
    public function accept(VisitorNode $visitorNode, \ArrayIterator $nodes): void
    {
        $visitorNode->visitFunction($this, $nodes);
    }

    public function getType(): string
    {
        return NodeGrammar::FUNCTION->name;
    }
}
