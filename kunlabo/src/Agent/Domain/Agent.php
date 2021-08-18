<?php

namespace Kunlabo\Agent\Domain;

use DateTime;
use Kunlabo\Agent\Domain\Event\AgentCreatedEvent;
use Kunlabo\Agent\Domain\Event\AgentFileCreatedEvent;
use Kunlabo\Agent\Domain\Event\AgentFileUpdatedEvent;
use Kunlabo\Shared\Domain\Aggregate\NamedAggregateRoot;
use Kunlabo\Shared\Domain\ValueObject\Name;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class Agent extends NamedAggregateRoot {
    private function __construct(
        Uuid $id,
        DateTime $created,
        DateTime $modified,
        Name $name,
        private Uuid $owner
    ) {
        parent::__construct($id, $created, $modified, $name);
    }

    public static function create(
        Uuid $id,
        Name $name,
        Uuid $owner
    ): self {
        $agent = new self($id, new DateTime(), new DateTime(), $name, $owner);
        $agent->record(new AgentCreatedEvent($agent));

        return $agent;
    }

    public function getOwner(): Uuid
    {
        return $this->owner;
    }

    public function addFile(string $path): AgentFile
    {
        $file = AgentFile::create(Uuid::random(), $this->id, $path);

        $this->update();
        $this->record(new AgentFileCreatedEvent($file));

        return $file;
    }

    public function updateFile(AgentFile $file): void
    {
        $file->update();

        $this->update();
        $this->record(new AgentFileUpdatedEvent($file));
    }
}