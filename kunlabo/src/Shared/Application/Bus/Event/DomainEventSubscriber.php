<?php

namespace Kunlabo\Shared\Application\Bus\Event;

interface DomainEventSubscriber
{
    public static function subscribedTo(): array;
}