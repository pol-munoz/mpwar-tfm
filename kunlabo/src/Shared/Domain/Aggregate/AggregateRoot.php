<?php


namespace Kunlabo\Shared\Domain\Aggregate;

use Kunlabo\Shared\Domain\Bus\Event\DomainEvent;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

abstract class AggregateRoot
{
    private array $domainEvents;

    public function __construct(protected Uuid $id)
    {
        $this->domainEvents = [];
    }

    public function getId(): Uuid
    {
        return $this->id;
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