<?php

namespace Kunlabo\Shared\Domain\Event;

use DateTimeImmutable;
use Kunlabo\Shared\Domain\Utils;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

abstract class DomainEvent
{
    private Uuid $eventId;

    public function __construct(private Uuid $aggregateId, Uuid $eventId = null)
    {
        $this->eventId = $eventId ?: Uuid::random();
    }

    public function getAggregateId(): Uuid
    {
        return $this->aggregateId;
    }

    public function getEventId(): string
    {
        return $this->eventId->getRaw();
    }
}