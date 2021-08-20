<?php

namespace Kunlabo\Participant\Application\Query;

use Kunlabo\Participant\Domain\Participant;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class ParticipantResponse implements Response
{
    public function __construct(private ?Participant $participant)
    {
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }
}