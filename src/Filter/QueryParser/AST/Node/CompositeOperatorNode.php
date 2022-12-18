<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\QueryParser\AST\Node;

use Ranky\SharedBundle\Filter\QueryParser\AST\NodeGrammar;
use Ranky\SharedBundle\Filter\QueryParser\AST\Visitor\VisitorNode;

/**
 * @property array{operator: \Ranky\SharedBundle\Filter\CompositeOperator} $attributes
 */
class CompositeOperatorNode extends Node
{

    /**
     * @param VisitorNode $visitorNode
     * @param \ArrayIterator<int, Node> $nodes
     * @return void
     */
    public function accept(VisitorNode $visitorNode, \ArrayIterator $nodes): void
    {
        $visitorNode->visitCompositeOperator($this, $nodes);
    }

    public function getType(): string
    {
        return NodeGrammar::COMPOSITE_OPERATOR->name;
    }
}
