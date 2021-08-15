<?php


namespace Kunlabo\Shared\Domain\Aggregate;

use DateTime;
use Kunlabo\Shared\Domain\Event\DomainEvent;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

abstract class AggregateRoot
{
    private array $domainEvents;

    protected function __construct(protected Uuid $id, protected DateTime $created, protected DateTime $modified)
    {
        $this->domainEvents = [];
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function getModified(): DateTime
    {
        return $this->modified;
    }

    final public function pullDomainEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }

    final protected function record(DomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }
}