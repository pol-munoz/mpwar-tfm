<?php

namespace Kunlabo\Shared\Domain\Event;

use DateTimeImmutable;
use Kunlabo\Shared\Domain\Utils;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

abstract class DomainEvent
{
    private Uuid $eventId;
    private string $occurredOn;

    public function __construct(private Uuid $aggregateId, Uuid $eventId = null, string $occurredOn = null)
    {
        $this->eventId = $eventId ?: Uuid::random();
        $this->occurredOn = $occurredOn ?: Utils::dateToString(new DateTimeImmutable());
    }

    public function getAggregateId(): string
    {
        return $this->aggregateId->getRaw();
    }

    public function getEventId(): string
    {
        return $this->eventId->getRaw();
    }

    public function getOccurredOn(): string
    {
        return $this->occurredOn;
    }
}