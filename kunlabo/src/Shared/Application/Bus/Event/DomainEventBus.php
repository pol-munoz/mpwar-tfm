<?php

namespace Kunlabo\Shared\Application\Bus\Event;

interface DomainEventBus
{
    public function publish(DomainEvent ...$events): void;
}