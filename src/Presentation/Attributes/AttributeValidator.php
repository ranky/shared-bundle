<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Presentation\Attributes;


final class AttributeValidator
{
    private string $type;
    private ?string $constraint;
    /** @var array<string>|null  */
    private ?array $groups;

    /**
     * @param string $type
     * @param string|null $constraint
     * @param array<string>|null $groups
     */
    public function __construct(
        string $type,
        string $constraint = null,
        ?array $groups = null
    ) {
        $this->type = $type;
        $this->constraint = $constraint;
        $this->groups = $groups;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function constraint(): ?string
    {
        return $this->constraint;
    }

    /**
     * @return string[]|null
     */
    public function groups(): ?array
    {
        return $this->groups;
    }

}
