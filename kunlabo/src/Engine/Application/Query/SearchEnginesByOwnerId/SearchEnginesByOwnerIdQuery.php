<?php

namespace Kunlabo\Engine\Application\Query\SearchEnginesByOwnerId;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class SearchEnginesByOwnerIdQuery implements Query
{

    private function __construct(private Uuid $ownerId)
    {
    }

    public static function fromOwnerId(Uuid $ownerId): self
    {
        return new self(
            $ownerId
        );
    }

    public function getOwnerId(): Uuid
    {
        return $this->ownerId;
    }
}