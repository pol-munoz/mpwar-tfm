<?php

namespace Kunlabo\Agent\Application;

use Kunlabo\Agent\Domain\Agent;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class AgentResponse implements Response
{
    public function __construct(private ?Agent $agent)
    {
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }
}