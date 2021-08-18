<?php

namespace Kunlabo\Agent\Application\Query\FindAgentById;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class FindAgentByIdQuery implements Query
{
    private function __construct(private Uuid $id)
    {
    }

    public static function fromId(Uuid $id): self
    {
        return new self(
            $id
        );
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}