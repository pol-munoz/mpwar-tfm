<?php

namespace Kunlabo\Log\Application\Query\SearchLogsByStudyAndParticipant;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class SearchLogsByStudyAndParticipantQuery implements Query
{
    private function __construct(private Uuid $studyId, private Uuid $participantId)
    {
    }

    public static function create(string $studyId, string $participantId): self
    {
        return new self(
            Uuid::fromRaw($studyId),
            Uuid::fromRaw($participantId)
        );
    }

    public function getStudyId(): Uuid
    {
        return $this->studyId;
    }

    public function getParticipantId(): Uuid
    {
        return $this->participantId;
    }
}