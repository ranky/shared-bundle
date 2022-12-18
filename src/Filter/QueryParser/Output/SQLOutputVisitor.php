<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\QueryParser\Output;

use Ranky\SharedBundle\Filter\Criteria;
use Ranky\SharedBundle\Filter\FilterFactory;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\CompositeOperatorNode;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\FunctionNode;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\Node;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\WhereCloseGroupNode;
use Ranky\SharedBundle\Filter\QueryParser\AST\Node\WhereOpenGroupNode;
use Ranky\SharedBundle\Filter\Visitor\CriteriaFilterVisitor;
use Ranky\SharedBundle\Filter\Visitor\SqlExpressionFilterVisitor;

class SQLOutputVisitor implements OutputVisitor
{

    private string $queryString = '';
    private Criteria $criteria;

    public function __construct(Criteria $criteria)
    {
        $this->criteria = $criteria;
    }

    public function getOutput(): string
    {
        return \trim(\preg_replace(
            ['/\s+/', '/\(\s+/', '/\s+\)/'],
            [' ', '(', ')'],
            $this->queryString
        ) ?? '');
    }

    public function visitWhereOpenGroup(WhereOpenGroupNode $node, \ArrayIterator $nodes): ?Node
    {
        $this->queryString .= '(';

        return $node;
    }

    public function visitWhereCloseGroup(WhereCloseGroupNode $node, \ArrayIterator $nodes): ?Node
    {
        $this->queryString .= ')';

        return $node;
    }

    public function visitFunction(FunctionNode $node, \ArrayIterator $nodes): ?Node
    {
        /** @var \Ranky\SharedBundle\Filter\ConditionOperator $functionName */
        $functionName = $node->getAttribute('name');
        $field        = $node->getAttribute('field');
        $value        = $node->getAttribute('value');

        $filter = FilterFactory::create($field, $functionName, $value);
        $criteriaFilterVisitor = new CriteriaFilterVisitor();
        $qlCriteriaFilterVisitor = new SqlExpressionFilterVisitor();
        $filter->accept($criteriaFilterVisitor, $this->criteria);
        $filter->accept($qlCriteriaFilterVisitor, $this->criteria);

        $this->queryString .= \sprintf(
            ' %s ',
            $filter->expression(),
        );

        return $node;
    }

    public function visitCompositeOperator(CompositeOperatorNode $node, \ArrayIterator $nodes): ?Node
    {
        /** @var \Ranky\SharedBundle\Filter\CompositeOperator $operator */
        $operator          = $node->getAttribute('operator');
        $this->queryString .= sprintf(' %s ', $operator->expression());

        return $node;
    }
}
