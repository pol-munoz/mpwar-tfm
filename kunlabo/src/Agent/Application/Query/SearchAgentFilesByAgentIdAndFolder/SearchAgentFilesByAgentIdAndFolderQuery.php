<?php

namespace Kunlabo\Agent\Application\Query\SearchAgentFilesByAgentIdAndFolder;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class SearchAgentFilesByAgentIdAndFolderQuery implements Query
{
    private function __construct(private Uuid $agentId, private string $folder)
    {
    }

    public static function create(string $agentId, string $folder): self
    {
        return new self(
            Uuid::fromRaw($agentId),
            $folder
        );
    }

    public function getAgentId(): Uuid
    {
        return $this->agentId;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }
}