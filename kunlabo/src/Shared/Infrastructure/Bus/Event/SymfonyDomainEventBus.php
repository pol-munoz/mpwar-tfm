<?php

namespace Kunlabo\Shared\Infrastructure\Bus\Event;

use Kunlabo\Shared\Application\Bus\Event\DomainEvent;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;

final class SymfonyDomainEventBus implements DomainEventBus
{
    public function __construct(private $eventBus)
    {
    }

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}