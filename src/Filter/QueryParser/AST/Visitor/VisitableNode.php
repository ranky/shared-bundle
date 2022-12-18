<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\QueryParser\AST\Visitor;


use Ranky\SharedBundle\Filter\QueryParser\AST\Node\Node;

interface VisitableNode
{
    /**
     * @param VisitorNode $visitorNode
     * @param \ArrayIterator<int, Node> $nodes
     * @return void
     */
    public function accept(VisitorNode $visitorNode, \ArrayIterator $nodes): void;
}
