<?php

namespace Kunlabo\Engine\Domain\Event;

use Kunlabo\Engine\Domain\EngineFile;
use Kunlabo\Shared\Domain\Event\DomainEvent;

final class EngineFileUpdatedEvent extends DomainEvent
{
    public function __construct(EngineFile $engineFile)
    {
        parent::__construct($engineFile->getId());
    }
}