<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\Event;

interface DomainEvent
{
    public function aggregateId(): string;

    /**
     * @return array<string, mixed>
     */
    public function payload(): array;

    public function occurredOn(): int;

    public function eventId(): string;

    public function eventClass(): string;

    public function eventName(): string;
}
