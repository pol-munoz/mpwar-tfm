<?php

namespace Kunlabo\User\Application\Query\FindUserById;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class FindUserByIdQuery implements Query
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