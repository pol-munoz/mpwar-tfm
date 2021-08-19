<?php

namespace Kunlabo\Study\Domain;

use DateTime;
use Kunlabo\Shared\Domain\Aggregate\NamedAggregateRoot;
use Kunlabo\Shared\Domain\ValueObject\Name;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\Study\Domain\Event\StudyCreatedEvent;

final class Study extends NamedAggregateRoot
{
    private function __construct(
        Uuid $id,
        DateTime $created,
        DateTime $modified,
        Name $name,
        private Uuid $owner,
        private Uuid $engine,
        private Uuid $agent
    ) {
        parent::__construct($id, $created, $modified, $name);
    }

    public static function create(
        Uuid $id,
        Name $name,
        Uuid $owner,
        Uuid $engine,
        Uuid $agent
    ): self {
        $study = new self($id, new DateTime(), new DateTime(), $name, $owner, $engine, $agent);
        $study->record(new StudyCreatedEvent($study));

        return $study;
    }

    public function getOwner(): Uuid
    {
        return $this->owner;
    }

    public function getEngine(): Uuid
    {
        return $this->engine;
    }

    public function getAgent(): Uuid
    {
        return $this->agent;
    }
}