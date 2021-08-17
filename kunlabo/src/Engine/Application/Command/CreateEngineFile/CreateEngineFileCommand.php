<?php

namespace Kunlabo\Engine\Application\Command\CreateEngineFile;

use Kunlabo\Shared\Application\Bus\Command\Command;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class CreateEngineFileCommand implements Command
{
    private function __construct(
        private Uuid $engine,
        private string $path
    ) {
    }

    public static function create(
        string $engine,
        string $path
    ): self {
        return new self(
            Uuid::fromRaw($engine),
            $path
        );
    }

    public function getEngine(): Uuid
    {
        return $this->engine;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}