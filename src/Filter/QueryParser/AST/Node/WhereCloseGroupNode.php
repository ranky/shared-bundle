<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\QueryParser\AST\Node;

use Ranky\SharedBundle\Filter\QueryParser\AST\NodeGrammar;
use Ranky\SharedBundle\Filter\QueryParser\AST\Visitor\VisitorNode;

class WhereCloseGroupNode extends Node
{
    /**
     * @param VisitorNode $visitorNode
     * @param \ArrayIterator<int, Node> $nodes
     * @return void
     */
    public function accept(VisitorNode $visitorNode, \ArrayIterator $nodes): void
    {
        $visitorNode->visitWhereCloseGroup($this, $nodes);
    }

    public function getType(): string
    {
        return NodeGrammar::WHERE_CLOSE_GROUP->name;
    }
}
