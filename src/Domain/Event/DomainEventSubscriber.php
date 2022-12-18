<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\Event;

interface DomainEventSubscriber
{
    /**
     * Add event class to handle. This uses the __invoke method
     * @return string
     */
    public static function subscribedTo(): string;

    /**
     * The higher the number, the earlier a subscriber is executed
     * 0 by default
     * @return int
     */
    public static function priority(): int;
}
