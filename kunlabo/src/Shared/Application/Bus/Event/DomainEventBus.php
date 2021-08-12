<?php

namespace Kunlabo\Shared\Application\Bus\Event;

use Kunlabo\Shared\Domain\Event\DomainEvent;

interface DomainEventBus
{
    public function publish(DomainEvent ...$events): void;
}