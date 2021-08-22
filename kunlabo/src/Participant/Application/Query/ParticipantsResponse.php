<?php

namespace Kunlabo\Participant\Application\Query;

use Kunlabo\Shared\Application\Bus\Query\Response;

final class ParticipantsResponse implements Response
{
    public function __construct(private array $participants)
    {
    }

    public function getParticipants(): array
    {
        return $this->participants;
    }
}