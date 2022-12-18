<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\QueryParser\AST\Node;

use Ranky\SharedBundle\Filter\QueryParser\AST\NodeGrammar;
use Ranky\SharedBundle\Filter\QueryParser\AST\Visitor\VisitorNode;

class WhereOpenGroupNode extends Node
{
    /**
     * @param VisitorNode $visitorNode
     * @param \ArrayIterator<int, Node> $nodes
     * @return void
     */
    public function accept(VisitorNode $visitorNode, \ArrayIterator $nodes): void
    {
        $visitorNode->visitWhereOpenGroup($this, $nodes);
        foreach ($this->nodes as $node){
            $node->accept($visitorNode, $nodes);
        }
    }

    public function getType(): string
    {
        return NodeGrammar::WHERE_OPEN_GROUP->name;
    }
}
