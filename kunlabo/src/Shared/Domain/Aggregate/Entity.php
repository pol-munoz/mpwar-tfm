<?php

namespace Kunlabo\Shared\Domain\Aggregate;

use DateTime;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

abstract class Entity
{
    protected function __construct(protected Uuid $id, protected DateTime $created, protected DateTime $modified)
    {
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

    public function update(): void
    {
        $this->modified = new DateTime();
    }
}