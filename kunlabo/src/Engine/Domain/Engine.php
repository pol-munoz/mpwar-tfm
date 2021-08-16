<?php

namespace Kunlabo\Engine\Domain;

use DateTime;
use Kunlabo\Engine\Domain\Event\EngineCreatedEvent;
use Kunlabo\Shared\Domain\Aggregate\NamedAggregateRoot;
use Kunlabo\Shared\Domain\ValueObject\Name;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class Engine extends NamedAggregateRoot {
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
        $engine = new self($id, new DateTime(), new DateTime(), $name, $owner);
        $engine->record(new EngineCreatedEvent($engine));

        return $engine;
    }

    public function getOwner(): Uuid
    {
        return $this->owner;
    }
}