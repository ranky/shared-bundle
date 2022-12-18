<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Presentation\Attributes\Body;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_PARAMETER)]
class Body
{
    private ?string $constraint;
    /** @var array<string>|null  */
    private ?array $groups;

    /**
     * @param string|null $constraint
     * @param array<string>|null $groups
     */
    public function __construct(?string $constraint = null, ?array $groups = null)
    {
        $this->constraint = $constraint;
        $this->groups     = $groups;
    }

    public function getConstraint(): ?string
    {
        return $this->constraint;
    }

    /**
     * @return string[]|null
     */
    public function getGroups(): ?array
    {
        return $this->groups;
    }

}
