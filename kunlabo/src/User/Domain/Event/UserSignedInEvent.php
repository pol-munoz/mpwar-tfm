<?php

namespace Kunlabo\User\Domain\Event;

use Kunlabo\Shared\Domain\Event\DomainEvent;
use Kunlabo\User\Domain\User;

final class UserSignedInEvent extends DomainEvent
{
    public function __construct(User $user)
    {
        parent::__construct($user->getId());
    }
}