<?php

namespace Kunlabo\Log\Domain;

// A bit weird since the creation is handled by Actions (with Monolog) and this is more tied to participant (with Elastic)
use Kunlabo\Shared\Domain\ValueObject\Uuid;

interface LogRepository
{
    public function readAllByStudyId(Uuid $studyId): array;
    public function readAllByStudyAndParticipantId(Uuid $studyId, Uuid $participantId): array;
}