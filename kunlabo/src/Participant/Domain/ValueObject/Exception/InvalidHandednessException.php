<?php

namespace Kunlabo\Participant\Domain\ValueObject\Exception;

use DomainException;

final class InvalidHandednessException extends DomainException
{
    public function __construct(string $handedness)
    {
        parent::__construct("Unsupported handedness: " . $handedness);
    }
}