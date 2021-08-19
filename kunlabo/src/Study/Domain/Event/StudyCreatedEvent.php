<?php

namespace Kunlabo\Study\Domain\Event;

use Kunlabo\Shared\Domain\Event\DomainEvent;
use Kunlabo\Study\Domain\Study;

final class StudyCreatedEvent extends DomainEvent
{
    public function __construct(Study $study)
    {
        parent::__construct($study->getId());
    }
}