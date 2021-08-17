<?php

namespace Kunlabo\User\Application\Query\SearchUserByEmail;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\User\Domain\ValueObject\Email;

final class SearchUserByEmailQuery implements Query
{
    private function __construct(private Email $email)
    {
    }

    public static function create(string $email): self
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
