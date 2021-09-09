<?php

namespace Kunlabo\Study\Domain;

use DateTime;
use Kunlabo\Shared\Domain\Aggregate\OwnedNamedAggregateRoot;
use Kunlabo\Shared\Domain\ValueObject\Name;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\Study\Domain\Event\StudyCreatedEvent;
use Kunlabo\Study\Domain\Event\StudyDeletedEvent;

final class Study extends OwnedNamedAggregateRoot
{
    private function __construct(
        Uuid $id,
        DateTime $created,
        DateTime $modified,
        Name $name,
        Uuid $owner,
        private Uuid $engineId,
        private Uuid $agentId
    ) {
        parent::__construct($id, $created, $modified, $name, $owner);
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

    public function getEngineId(): Uuid
    {
        return $this->engineId;
    }

    public function getAgentId(): Uuid
    {
        return $this->agentId;
    }

    public function delete(): void
    {
        $this->record(new StudyDeletedEvent($this));
    }
}