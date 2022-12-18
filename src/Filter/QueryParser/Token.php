<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\QueryParser;

class Token
{

    public function __construct(
        private readonly string $value,
        private readonly TokenGrammar $type,
        private readonly int $startPosition,
        private readonly int $endPosition
    ) {
    }

    public function value(): string
    {
        return $this->value;
    }


    public function type(): TokenGrammar
    {
        return $this->type;
    }


    public function endPosition(): int
    {
        return $this->endPosition;
    }


    public function startPosition(): int
    {
        return $this->startPosition;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'type' => $this->type->name,
            'startPosition' => $this->startPosition,
            'endPosition' => $this->endPosition,
        ];
    }

}
