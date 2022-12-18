<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\QueryParser\AST\Visitor;


use Ranky\SharedBundle\Filter\QueryParser\AST\Node\CompositeOperatorNode;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\FunctionNode;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\Node;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\WhereCloseGroupNode;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\WhereOpenGroupNode;

interface VisitorNode
{
    /**
     * @param WhereOpenGroupNode $node
     * @param \ArrayIterator<int,Node> $nodes
     * @return Node|null
     */
    public function visitWhereOpenGroup(WhereOpenGroupNode $node, \ArrayIterator $nodes): ?Node;

    /**
     * @param WhereCloseGroupNode $node
     * @param \ArrayIterator<int,Node> $nodes
     * @return Node|null
     */
    public function visitWhereCloseGroup(WhereCloseGroupNode $node, \ArrayIterator $nodes): ?Node;

    /**
     * @param FunctionNode $node
     * @param \ArrayIterator<int,Node> $nodes
     * @return Node|null
     */
    public function visitFunction(FunctionNode $node, \ArrayIterator $nodes): ?Node;
    /**
     * @param CompositeOperatorNode $node
     * @param \ArrayIterator<int,Node> $nodes
     * @return Node|null
     */
    public function visitCompositeOperator(CompositeOperatorNode $node, \ArrayIterator $nodes): ?Node;

}
