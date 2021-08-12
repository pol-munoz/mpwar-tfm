<?php

namespace Kunlabo\Shared\Domain\ValueObject\Exception;

use DomainException;

class InvalidUuidException extends DomainException
{
    public function __construct(string $id)
    {
        parent::__construct("Invalid UUID: " . $id);
    }
}