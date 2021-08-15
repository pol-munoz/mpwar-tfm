<?php

namespace Kunlabo\Shared\Domain\ValueObject\Exception;

use DomainException;

final class InvalidUuidException extends DomainException
{
    public function __construct(string $id)
    {
        parent::__construct("Invalid UUID: " . $id);
    }
}