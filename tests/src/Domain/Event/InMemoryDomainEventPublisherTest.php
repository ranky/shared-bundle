<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Domain\Event;

use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Domain\Event\DomainEvent;
use Ranky\SharedBundle\Domain\Event\DomainEventSubscriber;
use Ranky\SharedBundle\Domain\Event\InMemoryDomainEventPublisher;
use Ranky\SharedBundle\Domain\ValueObject\DomainEventId;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\PageDomainEvent;

class LowerPriorityDomainEventSubscriber implements DomainEventSubscriber
{
    public static function subscribedTo(): string
    {
        return PageDomainEvent::class;
    }

    public function __invoke(PageDomainEvent $customDomainEvent): bool
    {
        return false;
    }

    public static function priority(): int
    {
        return 0;
    }
}


class HigherPriorityDomainEventSubscriber implements DomainEventSubscriber
{
    public static function subscribedTo(): string
    {
        return PageDomainEvent::class;
    }

    public function __invoke(PageDomainEvent $customDomainEvent): bool
    {
        return true;
    }

    public static function priority(): int
    {
        return 1;
    }
}

class StdDomainEventSubscriber implements DomainEventSubscriber
{
    public static function subscribedTo(): string
    {
        return \stdClass::class;
    }

    public function __invoke(DomainEvent $domainEvent): bool
    {
        return true;
    }

    public static function priority(): int
    {
        return 5;
    }
}


class InMemoryDomainEventPublisherTest extends TestCase
{

    public function testItShouldCreateTwoSubscriberOfPageDomainEventAndPublishItInOrderOfPriority(): void
    {
        // Domain Event
        $aggregateId     = (string)DomainEventId::create();
        $pageDomainEvent = new PageDomainEvent($aggregateId);

        // Subscribers to Domain Event
        $higherPriorityDomainEventSubscriber = new HigherPriorityDomainEventSubscriber();
        $lowerPriorityDomainEventSubscriber  = new LowerPriorityDomainEventSubscriber();

        // Publisher subscriber by Domain Event
        $domainEventPublisher = new InMemoryDomainEventPublisher([
            $lowerPriorityDomainEventSubscriber,
            $higherPriorityDomainEventSubscriber,
            new StdDomainEventSubscriber(),
        ]);
        $domainEventPublisher->publish($pageDomainEvent);

        // check priority and the subscribersByDomainEvent method
        $this->assertSame(
            [$higherPriorityDomainEventSubscriber, $lowerPriorityDomainEventSubscriber],
            $domainEventPublisher->subscribersByDomainEvent($pageDomainEvent)
        );
    }
}
