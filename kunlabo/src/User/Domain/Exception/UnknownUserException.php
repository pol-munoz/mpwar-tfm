<?php

namespace Kunlabo\User\Domain\Exception;

use DomainException;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class UnknownUserException extends DomainException
{
    public function __construct(Uuid $identifier)
    {
        parent::__construct("Unknown user: " . $identifier);
    }
}