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
        private Uuid $engineId,
        private Uuid $agentId,
    ) {
    }

    public static function create(
        Uuid $uuid,
        string $name,
        Uuid $owner,
        string $engineId,
        string $agentId
    ): self {
        return new self(
            $uuid,
            Name::fromRaw($name),
            $owner,
            Uuid::fromRaw($engineId),
            Uuid::fromRaw($agentId)
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

    public function getEngineId(): Uuid
    {
        return $this->engineId;
    }

    public function getAgentId(): Uuid
    {
        return $this->agentId;
    }
}