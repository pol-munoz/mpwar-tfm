<?php


namespace Kunlabo\Shared\Domain\Aggregate;

use DateTime;
use Kunlabo\Shared\Domain\Event\DomainEvent;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

abstract class AggregateRoot extends Entity
{
    private array $domainEvents;

    protected function __construct(Uuid $id, DateTime $created, DateTime $modified)
    {
        parent::__construct($id, $created, $modified);
        $this->domainEvents = [];
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