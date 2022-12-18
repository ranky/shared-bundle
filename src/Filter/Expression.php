<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter;

class Expression
{
    private string $expression;
    /**
     * @var array<string, mixed>
     */
    private array $parameters;

    /**
     * @param string $expression
     * @param array<string, mixed> $parameters
     */
    public function __construct(string $expression = '', array $parameters = [])
    {
        $this->expression = $expression;
        $this->parameters = $parameters;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function setExpression(string $expression): void
    {
        $this->expression = $expression;
    }

    public function addParameter(string $key, mixed $value): void
    {
        $this->parameters[$key] = $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array<string,mixed> $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function resolve(): string
    {
        return \str_replace(
            \array_keys($this->parameters),
            \array_values($this->parameters),
            $this->expression
        );
    }

    public function __toString(): string
    {
        return $this->resolve();
    }

}
