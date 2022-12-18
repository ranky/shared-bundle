<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\QueryParser\AST\Visitor;

use Ranky\SharedBundle\Filter\ConditionOperator;
use Ranky\SharedBundle\Filter\CompositeOperator;
use Ranky\SharedBundle\Filter\FilterHelper;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\CompositeOperatorNode;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\FunctionNode;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\Node;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\WhereCloseGroupNode;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\WhereOpenGroupNode;
use Ranky\SharedBundle\Filter\QueryParser\Exception\NodeException;

class NormalizeNodeAttributesVisitor implements VisitorNode
{
    /**
     * @param WhereOpenGroupNode $node
     * @param \ArrayIterator<int, Node> $nodes
     * @return Node|null
     */
    public function visitWhereOpenGroup(WhereOpenGroupNode $node, \ArrayIterator $nodes): ?Node
    {
        return $node;
    }

    /**
     * @param WhereCloseGroupNode $node
     * @param \ArrayIterator<int, Node> $nodes
     * @return Node|null
     */
    public function visitWhereCloseGroup(WhereCloseGroupNode $node, \ArrayIterator $nodes): ?Node
    {
        return $node;
    }

    /**
     * @param FunctionNode $node
     * @param \ArrayIterator<int, Node> $nodes
     * @return Node|null
     */
    public function visitFunction(FunctionNode $node, \ArrayIterator $nodes): ?Node
    {
        $attributes = [];
        foreach ($node->getAttributes() as $name => $value) {
            $value = \str_replace(['\'', '"'], '', $value);

            $attributes[$name] = FilterHelper::castValue($value);
        }
        if ($attributes) {
            $node->setAttributes($attributes);
            $nameFunction   = $attributes['name'];
            $filterOperator = ConditionOperator::tryFrom($nameFunction);
            if (!$filterOperator) {
                throw new NodeException(
                    sprintf(
                        'The filter function operator "%s" is not valid. List of valid functions: %s',
                        $nameFunction,
                        \implode(',', ConditionOperator::operators())
                    )
                );
            }
            $node->setAttribute('name', $filterOperator);
        }

        return $node;
    }

    /**
     * @param CompositeOperatorNode $node
     * @param \ArrayIterator<int, Node> $nodes
     * @return Node|null
     */
    public function visitCompositeOperator(CompositeOperatorNode $node, \ArrayIterator $nodes): ?Node
    {
        $operator = $node->getAttribute('operator');
        $logicOperator = CompositeOperator::tryFrom($operator);
        if (!$logicOperator) {
            throw new NodeException(
                sprintf(
                    'The logic operator "%s" is not valid. List of valid logic operators: %s',
                    $operator,
                    \implode(',', CompositeOperator::operators())
                )
            );
        }
        $node->setAttribute('operator', $logicOperator);
        return $node;
    }
}
