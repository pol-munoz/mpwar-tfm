<?php

namespace Kunlabo\Participant\Domain\Event;

use Kunlabo\Participant\Domain\Participant;
use Kunlabo\Shared\Domain\Event\DomainEvent;

final class ParticipantFilledSurveyEvent extends DomainEvent
{
    public function __construct(Participant $participant)
    {
        parent::__construct($participant->getId());
    }
}