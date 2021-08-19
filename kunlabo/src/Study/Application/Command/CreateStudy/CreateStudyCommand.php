<?php

namespace Kunlabo\Study\Application\Command\CreateStudy;

use Kunlabo\Shared\Application\Bus\Command\Command;
use Kunlabo\Shared\Domain\ValueObject\Name;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class CreateStudyCommand implements Command
{
    private function __construct(
        private Uuid $uuid,
        private Name $name,
        private Uuid $owner,
        private Uuid $engine,
        private Uuid $agent,
    ) {
    }

    public static function create(
        Uuid $uuid,
        string $name,
        Uuid $owner,
        string $engine,
        string $agent
    ): self {
        return new self(
            $uuid,
            Name::fromRaw($name),
            $owner,
            Uuid::fromRaw($engine),
            Uuid::fromRaw($agent)
        );
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getName(): Name
    {
        return $this->name;
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