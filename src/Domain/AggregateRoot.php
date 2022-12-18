<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Domain;

use Ranky\SharedBundle\Domain\Event\DomainEvent;

abstract class AggregateRoot
{
    /**
     * @var DomainEvent[]
     */
    private array $events = [];

    protected function record(DomainEvent $domainEvent): void
    {
        $this->events[] = $domainEvent;
    }

    /**
     * @return DomainEvent[]
     */
    public function recordedEvents(): array
    {
       $events = $this->events;
       $this->events = [];

       return $events;
    }

}
