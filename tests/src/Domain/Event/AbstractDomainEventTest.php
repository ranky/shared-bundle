<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Domain\Event;


use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Domain\ValueObject\DomainEventId;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\PageDomainEvent;

class AbstractDomainEventTest extends TestCase
{

    public function testItShouldCreateValidDomainEventObject(): void
    {
        $aggregateId     = (string)DomainEventId::generate();
        $occurredOn      = new \DateTimeImmutable();
        $testDomainEvent = new PageDomainEvent(
            $aggregateId,
            [],
            null,
            $occurredOn
        );

        $this->assertSame($aggregateId, $testDomainEvent->aggregateId());
        $this->assertTrue((bool)DomainEventId::fromString($testDomainEvent->eventId()));
        $this->assertSame($occurredOn->getTimestamp(), $testDomainEvent->occurredOn());
        $this->assertSame($testDomainEvent::class, $testDomainEvent->eventClass());
        $this->assertSame('PageDomainEvent', $testDomainEvent->eventName());
    }

}
