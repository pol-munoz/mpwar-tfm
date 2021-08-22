<?php

namespace Kunlabo\Participant\Application\Query\SearchParticipantsByStudyId;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class SearchParticipantsByStudyIdQuery implements Query
{
    private function __construct(private Uuid $studyId)
    {
    }

    public static function create(string $studyId): self
    {
        return new self(
            Uuid::fromRaw($studyId)
        );
    }

    public function getStudyId(): Uuid
    {
        return $this->studyId;
    }
}