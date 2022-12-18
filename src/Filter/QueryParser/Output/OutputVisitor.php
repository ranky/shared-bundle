<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\QueryParser\Output;

use Ranky\SharedBundle\Filter\QueryParser\AST\Visitor\VisitorNode;

interface OutputVisitor extends VisitorNode
{
    public function getOutput(): mixed;
}
