<?php

namespace Kunlabo\Agent\Application\Command\CreateAgentFile;

use Kunlabo\Shared\Application\Bus\Command\Command;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class CreateAgentFileCommand implements Command
{
    private function __construct(
        private Uuid $agent,
        private string $path
    ) {
    }

    public static function create(
        string $agent,
        string $path
    ): self {
        return new self(
            Uuid::fromRaw($agent),
            $path
        );
    }

    public function getAgentId(): Uuid
    {
        return $this->agent;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}