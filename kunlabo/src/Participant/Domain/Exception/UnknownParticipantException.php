<?php

namespace Kunlabo\Participant\Domain\Exception;

use DomainException;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class UnknownParticipantException extends DomainException
{
    public function __construct(Uuid $identifier)
    {
        parent::__construct("Unknown participant: " . $identifier);
    }
}