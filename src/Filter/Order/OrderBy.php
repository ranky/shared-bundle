<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\Order;


class OrderBy
{

    public const DEFAULT_ORDER_FIELD     = 'createdAt';
    public const DEFAULT_ORDER_DIRECTION = 'DESC';
    public const ASC                     = 'ASC';
    public const DESC                    = 'DESC';
    public const DIRECTIONS              = [self::ASC, self::DESC];


    public function __construct(private readonly string $field, private readonly string $direction)
    {
        if (!\in_array($direction, self::DIRECTIONS, true)) {
            throw new \InvalidArgumentException(
                \sprintf('The address value must be %s OR %s, %s has been provided.', self::ASC, self::DESC, $direction)
            );
        }
    }

    public function field(): string
    {
        return $this->field;
    }

    public function withField(string $field): self
    {
        return new self($field, $this->direction);
    }

    public function direction(): string
    {
        return $this->direction;
    }

    /**
     * @return string[]
     */
    public function asArray(): array
    {
        return [$this->field => $this->direction];
    }

    /**
     * @param array<string> $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            $data['field'] ?? self::DEFAULT_ORDER_FIELD,
            $data['direction'] ? \mb_strtoupper($data['direction']) : self::DEFAULT_ORDER_DIRECTION
        );
    }
}
