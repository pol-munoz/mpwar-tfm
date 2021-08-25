<?php

namespace Kunlabo\Engine\Application\Query\SearchEngineFilesByEngineId;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class SearchEngineFilesByEngineIdQuery implements Query
{
    private function __construct(private Uuid $engineId)
    {
    }

    public static function create(string $engineId): self
    {
        return new self(
            Uuid::fromRaw($engineId)
        );
    }

    public function getEngineId(): Uuid
    {
        return $this->engineId;
    }
}