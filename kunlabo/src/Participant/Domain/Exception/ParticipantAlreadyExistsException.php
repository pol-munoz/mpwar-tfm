<?php

namespace Kunlabo\Participant\Domain\Exception;

use DomainException;

final class ParticipantAlreadyExistsException extends DomainException
{
    public function __construct()
    {
        parent::__construct("This user already exists");
    }
}