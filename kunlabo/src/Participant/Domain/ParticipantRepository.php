<?php

namespace Kunlabo\Participant\Domain;

use Kunlabo\Shared\Domain\ValueObject\Uuid;

interface ParticipantRepository
{
    public function create(Participant $participant): void;

    public function readById(Uuid $id): ?Participant;
}