<?php

namespace Kunlabo\Engine\Application\Command\DeleteEngine;

use Kunlabo\Shared\Application\Bus\Command\Command;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class DeleteEngineCommand implements Command
{
    private function __construct(
        private Uuid $id
    ) {
    }

    public static function create(
        string $id,
    ): self {
        return new self(
            Uuid::fromRaw($id)
        );
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}