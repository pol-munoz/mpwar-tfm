<?php

namespace Kunlabo\Study\Application\Query\FindStudyById;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class FindStudyByIdQuery implements Query
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