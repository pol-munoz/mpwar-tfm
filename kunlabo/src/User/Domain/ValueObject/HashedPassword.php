<?php

namespace Kunlabo\User\Domain\ValueObject;

use Kunlabo\User\Domain\ValueObject\Exception\InvalidPasswordException;
use RuntimeException;

final class HashedPassword
{
    public const ALGO = PASSWORD_BCRYPT;
    public const OPTIONS = ['cost' => 12];

    private function __construct(private string $hash)
    {
    }

    public static function fromPlain(string $plain): self
    {
        if (strlen($plain) < 8 || strlen($plain) > 72) {
            throw new InvalidPasswordException("Must be between 8 and 72 characters");
        }

        return new self(self::hash($plain));
    }

    public static function fromHash(string $hash): self
    {
        return new self($hash);
    }

    private static function hash(string $plain): string
    {
        $hash = password_hash($plain, self::ALGO, self::OPTIONS);

        if (is_bool($hash)) {
            throw new RuntimeException('An error ocurred while hashing the password');
        }

        return $hash;
    }

    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash,self::ALGO, self::OPTIONS);
    }

    public function match(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->hash);
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}