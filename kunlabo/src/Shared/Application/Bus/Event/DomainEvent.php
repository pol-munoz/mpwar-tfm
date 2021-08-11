<?php

namespace Kunlabo\Shared\Application\Bus\Event;

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
        return $this->aggregateId;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function getOccurredOn(): string
    {
        return $this->occurredOn;
    }
}