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
        $this->eventId    = $eventId ?: Uuid::random();
        $this->occurredOn = $occurredOn ?: Utils::dateToString(new DateTimeImmutable());
    }

    abstract public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): self;

    abstract public static function eventName(): string;

    abstract public function toPrimitives(): array;

    public function getAggregateId(): string
    {
        return $this->aggregateId;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function occurredOn(): string
    {
        return $this->occurredOn;
    }
}