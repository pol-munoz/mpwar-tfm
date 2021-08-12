<?php

namespace Kunlabo\User\Domain\ValueObject\Exception;

use DomainException;

final class InvalidEmailException extends DomainException
{
    public function __construct(string $email)
    {
        parent::__construct("Invalid email: " . $email);
    }
}