<?php

namespace Kunlabo\Agent\Domain\Event;

use Kunlabo\Agent\Domain\AgentFile;
use Kunlabo\Shared\Domain\Event\DomainEvent;

final class AgentFileDeletedEvent extends DomainEvent
{
    public function __construct(AgentFile $agentFile)
    {
        parent::__construct($agentFile->getId());
    }
}