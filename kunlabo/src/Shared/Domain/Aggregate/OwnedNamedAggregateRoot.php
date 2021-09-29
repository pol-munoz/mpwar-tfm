<?php


namespace Kunlabo\Shared\Domain\Aggregate;

use DateTime;
use Kunlabo\Shared\Domain\ValueObject\Name;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

abstract class OwnedNamedAggregateRoot extends NamedAggregateRoot
{
    protected function __construct(Uuid $id, DateTime $created, DateTime $modified, Name $name, protected Uuid $owner)
    {
        parent::__construct($id, $created, $modified, $name);
    }

    public function getOwner(): Uuid
    {
        return $this->owner;
    }

    public function isOwnedBy(Uuid $id): bool
    {
        return $this->owner->getRaw() === $id->getRaw();
    }
}