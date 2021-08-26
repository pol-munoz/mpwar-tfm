<?php

namespace Kunlabo\Agent\Application\Command\DeleteAgent;

use Kunlabo\Shared\Application\Bus\Command\Command;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class DeleteAgentCommand implements Command
{
    private function __construct(
        private Uuid $id
    ) {
    }

    public static function create(
        string $id,
    ): self {
        return new self(
            Uuid::fromRaw($id),
        );
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}