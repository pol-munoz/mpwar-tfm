<?php

namespace Kunlabo\Agent\Application\Command\CreateAgent;

use Kunlabo\Shared\Application\Bus\Command\Command;
use Kunlabo\Shared\Domain\ValueObject\Name;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class CreateAgentCommand implements Command
{
    private function __construct(
        private Uuid $uuid,
        private Name $name,
        private Uuid $owner,
    ) {
    }

    public static function create(
        Uuid $uuid,
        string $name,
        Uuid $owner
    ): self {
        return new self(
            $uuid,
            Name::fromRaw($name),
            $owner
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
}