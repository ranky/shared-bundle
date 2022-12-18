<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\Event;

use Ranky\SharedBundle\Infrastructure\DependencyInjection\SharedBundleExtension;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class InMemoryDomainEventPublisher implements DomainEventPublisher
{

    /**
     * @var array<DomainEventSubscriber>
     */
    private array $domainEventSubscribers;

    /**
     * @param iterable<DomainEventSubscriber> $domainEventSubscribers
     */
    public function __construct(
        #[TaggedIterator(SharedBundleExtension::TAG_DOMAIN_EVENT_SUBSCRIBER)] iterable $domainEventSubscribers
    ) {
        $this->domainEventSubscribers = $domainEventSubscribers instanceof \Traversable
            ? \iterator_to_array($domainEventSubscribers)
            : $domainEventSubscribers;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribers(): array
    {
        return $this->domainEventSubscribers;
    }

    public function addSubscriber(DomainEventSubscriber $domainEventSubscriber): void
    {
        $this->domainEventSubscribers[] = $domainEventSubscriber;
    }

    public function removeSubscriber(DomainEventSubscriber $domainEventSubscriber): void
    {
        foreach ($this->domainEventSubscribers as $key => $subscriber) {
            if ($domainEventSubscriber::class === $subscriber::class) {
                unset($this->domainEventSubscribers[$key]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function publish(DomainEvent ...$domainEvents): void
    {
        foreach ($domainEvents as $domainEvent) {
            $eventsSubscribers = $this->subscribersByDomainEvent($domainEvent);
            foreach ($eventsSubscribers as $eventSubscriber) {
                if (!\method_exists($eventSubscriber, '__invoke')) {
                    throw new DomainEventException(
                        \sprintf(
                            'The event subscriber "%s" does not have the __invoke method',
                            $eventSubscriber::class
                        )
                    );
                }
                $eventSubscriber->__invoke($domainEvent);
            }
        }
    }

    /**
     * @param DomainEvent $domainEvent
     * @return array<DomainEventSubscriber>
     */
    public function subscribersByDomainEvent(DomainEvent $domainEvent): array
    {
        $subscribers = \array_filter(
            $this->domainEventSubscribers,
            static fn($subscriber) => $subscriber::subscribedTo()=== $domainEvent::class,
        );

        \usort($subscribers, static function ($a, $b) {
            $aPriority = \method_exists($a, 'priority') ? $a::priority() : 0;
            $bPriority = \method_exists($b, 'priority') ? $b::priority() : 0;

            return ($aPriority > $bPriority) ? -1 : 1;
        });

        return $subscribers;
    }


}
