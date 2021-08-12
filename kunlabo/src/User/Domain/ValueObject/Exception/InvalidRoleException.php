<?php

namespace Kunlabo\User\Domain\ValueObject\Exception;

use DomainException;

final class InvalidRoleException extends DomainException
{
    public function __construct(string $role)
    {
        parent::__construct("Invalid role: " . $role);
    }
}