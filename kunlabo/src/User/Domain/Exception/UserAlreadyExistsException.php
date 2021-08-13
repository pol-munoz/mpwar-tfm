<?php

namespace Kunlabo\User\Domain\Exception;

use DomainException;

final class UserAlreadyExistsException extends DomainException
{
    public function __construct()
    {
        parent::__construct("This email is taken");
    }
}