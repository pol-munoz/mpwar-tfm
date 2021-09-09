<?php

namespace Kunlabo\Agent\Domain;

use DateTime;
use Kunlabo\Agent\Domain\Event\AgentCreatedEvent;
use Kunlabo\Agent\Domain\Event\AgentDeletedEvent;
use Kunlabo\Agent\Domain\Event\AgentFileCreatedEvent;
use Kunlabo\Agent\Domain\Event\AgentFileDeletedEvent;
use Kunlabo\Agent\Domain\Event\AgentFileUpdatedEvent;
use Kunlabo\Agent\Domain\Event\AgentMainPathSetEvent;
use Kunlabo\Agent\Domain\ValueObject\AgentKind;
use Kunlabo\Shared\Domain\Aggregate\OwnedNamedAggregateRoot;
use Kunlabo\Shared\Domain\ValueObject\Name;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class Agent extends OwnedNamedAggregateRoot {
    private function __construct(
        Uuid $id,
        DateTime $created,
        DateTime $modified,
        Name $name,
        Uuid $owner,
        private AgentKind $kind,
        private string $main
    ) {
        parent::__construct($id, $created, $modified, $name, $owner);
    }

    public static function create(
        Uuid $id,
        Name $name,
        Uuid $owner,
        AgentKind $kind,
    ): self {
        $agent = new self($id, new DateTime(), new DateTime(), $name, $owner, $kind, $kind->getDefaultFile());
        $agent->record(new AgentCreatedEvent($agent));

        return $agent;
    }

    public function getKind(): AgentKind
    {
        return $this->kind;
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

    public function getMainUrl(): string
    {
        return AgentFile::BASE_PATH . $this->id . $this->main;
    }

    public function deleteFile(AgentFile $file): void
    {
        if ($this->main === $file->getPath()) {
            $this->main = $this->kind->getDefaultFile();
        }

        $this->update();
        $this->record(new AgentFileDeletedEvent($file));
    }

    public function delete(): void
    {
        $this->record(new AgentDeletedEvent($this));
    }
}