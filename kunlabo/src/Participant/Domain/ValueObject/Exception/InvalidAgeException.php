<?php

namespace Kunlabo\Participant\Domain\ValueObject\Exception;

use DomainException;

final class InvalidAgeException extends DomainException
{
    public function __construct(int $age)
    {
        parent::__construct("Invalid age: " . $age);
    }
}