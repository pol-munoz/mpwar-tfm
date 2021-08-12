<?php

namespace Kunlabo\User\Application\Query\FindByEmail;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\User\Domain\ValueObject\Email;

final class FindByEmailQuery implements Query
{
    private function __construct(private Email $email)
    {
    }

    public static function fromRaw(string $email): self
    {
        return new self(
            Email::fromRaw($email)
        );
    }

    public function getEmail(): Email
    {
        return $this->email;
    }
}
