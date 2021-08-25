<?php

namespace Kunlabo\Agent\Application\Query\SearchAgentFilesByAgentId;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class SearchAgentFilesByAgentIdQuery implements Query
{
    private function __construct(private Uuid $agentId)
    {
    }

    public static function create(string $agentId): self
    {
        return new self(
            Uuid::fromRaw($agentId)
        );
    }

    public function getAgentId(): Uuid
    {
        return $this->agentId;
    }
}