<?php

namespace Kunlabo\User\Domain;

use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\User\Domain\ValueObject\Email;

interface UserRepository
{
    public function create(User $user): void;

    public function readById(Uuid $id): ?User;
    public function readByEmail(Email $email): ?User;
}