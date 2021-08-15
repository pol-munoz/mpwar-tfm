<?php

namespace Kunlabo\Engine\Domain\Event;

use Kunlabo\Engine\Domain\Engine;
use Kunlabo\Shared\Domain\Event\DomainEvent;

final class EngineCreatedEvent extends DomainEvent
{
    public function __construct(Engine $engine)
    {
        parent::__construct($engine->getId());
    }

}