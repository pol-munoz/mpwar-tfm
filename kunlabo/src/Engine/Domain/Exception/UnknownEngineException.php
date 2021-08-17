<?php

namespace Kunlabo\Engine\Domain\Exception;

use DomainException;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class UnknownEngineException extends DomainException
{
    public function __construct(Uuid $identifier)
    {
        parent::__construct("Unknown engine: " . $identifier);
    }
}