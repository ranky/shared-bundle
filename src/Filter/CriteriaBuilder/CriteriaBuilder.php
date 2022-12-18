<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\CriteriaBuilder;

use Ranky\SharedBundle\Filter\Visitor\FilterVisitor;

interface CriteriaBuilder
{
    /**
     * @param FilterVisitor $filterVisitor
     * @return $this
     *
     */
    public function addFilterVisitor(FilterVisitor $filterVisitor): self;

    public function where(): self;

    public function withLimit(): self;

    public function withOrder(): self;

    public function getQuery(): mixed;
}
