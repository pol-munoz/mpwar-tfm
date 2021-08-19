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
        private Uuid $engineId,
        private Uuid $agentId
    ) {
        parent::__construct($id, $created, $modified, $name);
    }

    public static function create(
        Uuid $id,
        Name $name,
        Uuid $owner,
        Uuid $engineId,
        Uuid $agentId
    ): self {
        $study = new self($id, new DateTime(), new DateTime(), $name, $owner, $engineId, $agentId);
        $study->record(new StudyCreatedEvent($study));

        return $study;
    }

    public function getOwner(): Uuid
    {
        return $this->owner;
    }

    public function getEngineId(): Uuid
    {
        return $this->engineId;
    }

    public function getAgentId(): Uuid
    {
        return $this->agentId;
    }
}