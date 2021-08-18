<?php

namespace Kunlabo\Agent\Domain\Exception;

use DomainException;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class UnknownAgentException extends DomainException
{
    public function __construct(Uuid $identifier)
    {
        parent::__construct("Unknown agent: " . $identifier);
    }
}