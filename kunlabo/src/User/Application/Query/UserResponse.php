<?php

namespace Kunlabo\User\Application\Query;

use Kunlabo\Shared\Application\Bus\Query\Response;
use Kunlabo\User\Domain\User;

final class UserResponse implements Response
{
    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}