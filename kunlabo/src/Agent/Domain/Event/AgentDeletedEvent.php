<?php

namespace Kunlabo\Agent\Domain\Event;

use Kunlabo\Agent\Domain\Agent;
use Kunlabo\Shared\Domain\Event\DomainEvent;

final class AgentDeletedEvent extends DomainEvent
{
    public function __construct(Agent $agent)
    {
        parent::__construct($agent->getId());
    }
}