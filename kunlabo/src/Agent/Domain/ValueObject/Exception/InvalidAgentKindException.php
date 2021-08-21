<?php

namespace Kunlabo\Agent\Domain\ValueObject\Exception;

use DomainException;

final class InvalidAgentKindException extends DomainException
{
    public function __construct(string $kind)
    {
        parent::__construct("Invalid agent kind: " . $kind);
    }
}