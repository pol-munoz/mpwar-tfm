<?php

namespace Kunlabo\Agent\Application\Query\SearchAgentsByOwnerId;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class SearchAgentsByOwnerIdQuery implements Query
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