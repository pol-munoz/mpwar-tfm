<?php

namespace Kunlabo\Log\Application\Query\SearchNewLogsByStudy;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class SearchNewLogsByStudyQuery implements Query
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