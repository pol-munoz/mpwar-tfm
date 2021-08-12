<?php

namespace Kunlabo\User\Domain\Exception;

use DomainException;

final class UnknownUserException extends DomainException
{
    public function __construct(string $identifier)
    {
        parent::__construct("Unknown user: " . $identifier);
    }
}