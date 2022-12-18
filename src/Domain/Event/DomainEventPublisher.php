<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\Event;

/**
 * EventBus or EventDispatcher or EventPublisher
 */
interface DomainEventPublisher
{

    /** @return DomainEventSubscriber[] */
    public function getSubscribers(): array;

    public function addSubscriber(DomainEventSubscriber $domainEventSubscriber): void;

    public function removeSubscriber(DomainEventSubscriber $domainEventSubscriber): void;

    /**
     * Publish subscribers by Domain Event
     *
     * ```
     * $this->domainEventPublisher->publish(...$domain->recordedEvents())
     * ```
     *
     * @param DomainEvent ...$domainEvents
     * @return void
     */
    public function publish(DomainEvent ...$domainEvents): void;
}
