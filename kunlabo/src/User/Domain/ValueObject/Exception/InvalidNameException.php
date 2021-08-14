<?php

namespace Kunlabo\User\Domain\ValueObject\Exception;

use DomainException;

final class InvalidNameException extends DomainException
{
    public function __construct($reason)
    {
        parent::__construct($reason);
    }
}