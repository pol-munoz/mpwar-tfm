<?php

namespace Kunlabo\Action\Domain\ValueObject\Exception;

use DomainException;

final class InvalidActionException extends DomainException
{
    public function __construct(string $action)
    {
        parent::__construct("Invalid action: " . $action);
    }
}