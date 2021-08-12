<?php

namespace Kunlabo\Shared\Infrastructure\Bus\Event;

use Kunlabo\Shared\Domain\Event\DomainEvent;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;
use Symfony\Component\Messenger\MessageBusInterface;

final class SymfonyDomainEventBus implements DomainEventBus
{
    public function __construct(private MessageBusInterface $eventBus)
    {
    }

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}