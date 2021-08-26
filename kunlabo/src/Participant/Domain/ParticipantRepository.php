<?php

namespace Kunlabo\Participant\Domain;

use Kunlabo\Shared\Domain\ValueObject\Uuid;

interface ParticipantRepository
{
    public function create(Participant $participant): void;

    public function readById(Uuid $id): ?Participant;
    public function readAllForStudy(Uuid $study): array;

    public function delete(Participant $participant): void;

    public function update(Participant $participant): void;
}