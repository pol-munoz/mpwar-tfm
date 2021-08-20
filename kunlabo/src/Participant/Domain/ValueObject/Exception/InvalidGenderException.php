<?php

namespace Kunlabo\Participant\Domain\ValueObject\Exception;

use DomainException;

final class InvalidGenderException extends DomainException
{
    public function __construct(string $gender)
    {
        parent::__construct("Unsupported gender: " . $gender);
    }
}