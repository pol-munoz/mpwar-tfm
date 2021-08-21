<?php

namespace Kunlabo\Agent\Domain;

use DateTime;
use Kunlabo\Agent\Domain\Event\AgentCreatedEvent;
use Kunlabo\Agent\Domain\Event\AgentFileCreatedEvent;
use Kunlabo\Agent\Domain\Event\AgentFileUpdatedEvent;
use Kunlabo\Agent\Domain\Event\AgentMainPathSetEvent;
use Kunlabo\Shared\Domain\Aggregate\NamedAggregateRoot;
use Kunlabo\Shared\Domain\ValueObject\Name;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class Agent extends NamedAggregateRoot {
    private function __construct(
        Uuid $id,
        DateTime $created,
        DateTime $modified,
        Name $name,
        private Uuid $owner,
        private string $main
    ) {
        parent::__construct($id, $created, $modified, $name);
    }

    public static function create(
        Uuid $id,
        Name $name,
        Uuid $owner
    ): self {
        // TODO change to main.py if type is ai (actually, just ask the valueobject)
        $agent = new self($id, new DateTime(), new DateTime(), $name, $owner, '/index.html');
        $agent->record(new AgentCreatedEvent($agent));

        return $agent;
    }

    public function getOwner(): Uuid
    {
        return $this->owner;
    }

    public function getMain(): string
    {
        return $this->main;
    }

    public function setMain(string $path): void
    {
        $this->main = $path;
        $this->update();
        $this->record(new AgentMainPathSetEvent($this));
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