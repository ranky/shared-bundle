<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\Event;

use Ranky\SharedBundle\Domain\ValueObject\DomainEventId;

abstract class AbstractDomainEvent implements DomainEvent
{
    /**
     * @var array<string, mixed>
     */
    private array $payload;
    private string $aggregateId;
    private string $eventId;
    private int $occurredOn;
    private string $eventClass;
    private string $eventName;

    /**
     * @param string $aggregateId
     * @param array<string, mixed> $payload
     * @param string|null $eventId
     * @param \DateTimeImmutable|null $occurredOn
     */
    public function __construct(
        string $aggregateId,
        array $payload = [],
        string $eventId = null,
        \DateTimeImmutable $occurredOn = null
    ) {
        $this->aggregateId = $aggregateId;
        $this->payload     = $payload;
        $this->eventId     = $eventId ?? (string)DomainEventId::generate();
        $this->occurredOn  = $occurredOn ? $occurredOn->getTimestamp() : \time();
        $this->eventClass  = static::class;
        $this->eventName   = (new \ReflectionClass($this))->getShortName();
    }

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function occurredOn(): int
    {
        return $this->occurredOn;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->payload;
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function eventClass(): string
    {
        return $this->eventClass;
    }

    public function eventName(): string
    {
        return $this->eventName;
    }
}
