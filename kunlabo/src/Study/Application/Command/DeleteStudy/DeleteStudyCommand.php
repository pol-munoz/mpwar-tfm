<?php

namespace Kunlabo\Study\Application\Command\DeleteStudy;

use Kunlabo\Shared\Application\Bus\Command\Command;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class DeleteStudyCommand implements Command
{
    private function __construct(
        private Uuid $id,
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