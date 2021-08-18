<?php

namespace Kunlabo\Agent\Application;

use Kunlabo\Shared\Application\Bus\Query\Response;

final class AgentsResponse implements Response
{
    public function __construct(private array $agents)
    {
    }

    public function getAgents(): array
    {
        return $this->agents;
    }
}