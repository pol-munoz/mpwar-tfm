<?php

namespace Kunlabo\Study\Domain\Exception;

use DomainException;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class UnknownStudyException extends DomainException
{
    public function __construct(Uuid $identifier)
    {
        parent::__construct("Unknown study: " . $identifier);
    }
}