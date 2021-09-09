<?php

namespace Kunlabo\Participant\Application\Query\SearchNewParticipantsByStudyId;
use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class SearchNewParticipantsByStudyIdQuery implements Query
{
    private function __construct(private Uuid $studyId)
    {
    }

    public static function fromStudyId(Uuid $studyId): self
    {
        return new self(
            $studyId
        );
    }

    public function getStudyId(): Uuid
    {
        return $this->studyId;
    }
}