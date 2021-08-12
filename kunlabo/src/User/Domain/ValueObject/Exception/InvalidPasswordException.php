<?php

namespace Kunlabo\User\Domain\ValueObject\Exception;

use DomainException;

final class InvalidPasswordException extends DomainException
{
    public function __construct(string $reason)
    {
        parent::__construct("Invalid password: " . $reason);
    }
}