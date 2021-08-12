<?php

namespace Kunlabo\User\Domain\Exception;

use DomainException;
use Throwable;

final class InvalidCredentialsException extends DomainException
{
    public function __construct()
    {
        parent::__construct("Invalid credentials entered");
    }
}