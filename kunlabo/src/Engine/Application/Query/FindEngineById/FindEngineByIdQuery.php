<?php

namespace Kunlabo\Engine\Application\Query\FindEngineById;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class FindEngineByIdQuery implements Query
{
    private function __construct(private Uuid $id)
    {
    }

    public static function create(string $id): self
    {
        return new self(
            Uuid::fromRaw($id)
        );
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}