<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\Visitor\Extension;

use Ranky\SharedBundle\Filter\ConditionFilter;
use Ranky\SharedBundle\Filter\Criteria;

interface FilterExtensionVisitor
{
    public const TAG_NAME = 'ranky.filter_extension_visitor';

    /**
     * @param \Ranky\SharedBundle\Filter\ConditionFilter $filter
     * @param \Ranky\SharedBundle\Filter\Criteria $criteria
     * @return \Ranky\SharedBundle\Filter\ConditionFilter
     */
    public function visit(ConditionFilter $filter, Criteria $criteria): ConditionFilter;

    /***
     * @param \Ranky\SharedBundle\Filter\ConditionFilter $filter
     * @param \Ranky\SharedBundle\Filter\Criteria $criteria
     * @return bool
     */
    public function support(ConditionFilter $filter, Criteria $criteria): bool;

    public static function driver(): string;
}
