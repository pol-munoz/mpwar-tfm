<?php

namespace Kunlabo\Agent\Application\Command\CreateAgent;

use Kunlabo\Agent\Domain\ValueObject\AgentKind;
use Kunlabo\Shared\Application\Bus\Command\Command;
use Kunlabo\Shared\Domain\ValueObject\Name;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class CreateAgentCommand implements Command
{
    private function __construct(
        private Uuid $uuid,
        private Name $name,
        private Uuid $owner,
        private AgentKind $kind,
    ) {
    }

    public static function create(
        Uuid $uuid,
        string $name,
        Uuid $owner,
        string $kind
    ): self {
        return new self(
            $uuid,
            Name::fromRaw($name),
            $owner,
            AgentKind::fromRaw($kind)
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

    public function getKind(): AgentKind
    {
        return $this->kind;
    }
}