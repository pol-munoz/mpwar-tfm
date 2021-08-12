<?php

namespace Kunlabo\User\Infrastructure\Framework\Auth;

use Kunlabo\User\Domain\ValueObject\HashedPassword;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

final class PasswordHasher implements PasswordHasherInterface
{
    public function hash(string $plainPassword): string
    {
        return HashedPassword::fromPlain($plainPassword)->getHash();
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        return HashedPassword::fromHash($hashedPassword)->match($plainPassword);
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return HashedPassword::needsRehash($hashedPassword);
    }
}