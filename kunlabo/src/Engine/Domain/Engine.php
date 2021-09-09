<?php

namespace Kunlabo\Engine\Domain;

use DateTime;
use Kunlabo\Engine\Domain\Event\EngineCreatedEvent;
use Kunlabo\Engine\Domain\Event\EngineDeletedEvent;
use Kunlabo\Engine\Domain\Event\EngineFileCreatedEvent;
use Kunlabo\Engine\Domain\Event\EngineFileDeletedEvent;
use Kunlabo\Engine\Domain\Event\EngineFileUpdatedEvent;
use Kunlabo\Engine\Domain\Event\EngineMainPathSetEvent;
use Kunlabo\Shared\Domain\Aggregate\OwnedNamedAggregateRoot;
use Kunlabo\Shared\Domain\ValueObject\Name;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class Engine extends OwnedNamedAggregateRoot {
    private function __construct(
        Uuid $id,
        DateTime $created,
        DateTime $modified,
        Name $name,
        Uuid $owner,
        private string $main
    ) {
        parent::__construct($id, $created, $modified, $name, $owner);
    }

    public static function create(
        Uuid $id,
        Name $name,
        Uuid $owner
    ): self {
        $engine = new self($id, new DateTime(), new DateTime(), $name, $owner, '/index.html');
        $engine->record(new EngineCreatedEvent($engine));

        return $engine;
    }

    public function getMain(): string
    {
        return $this->main;
    }

    public function setMain(string $path): void
    {
        $this->main = $path;
        $this->update();
        $this->record(new EngineMainPathSetEvent($this));
    }

    public function addFile(string $path): EngineFile
    {
        $file = EngineFile::create(Uuid::random(), $this->id, $path);

        $this->update();
        $this->record(new EngineFileCreatedEvent($file));

        return $file;
    }

    public function updateFile(EngineFile $file): void
    {
        $file->update();

        $this->update();
        $this->record(new EngineFileUpdatedEvent($file));
    }

    public function getMainUrl(): string
    {
        return EngineFile::BASE_PATH . $this->id . $this->main;
    }

    public function deleteFile(EngineFile $file): void
    {
        if ($this->main === $file->getPath()) {
            $this->main = '/index.html';
        }

        $this->update();
        $this->record(new EngineFileDeletedEvent($file));
    }

    public function delete(): void
    {
        $this->record(new EngineDeletedEvent($this));
    }
}