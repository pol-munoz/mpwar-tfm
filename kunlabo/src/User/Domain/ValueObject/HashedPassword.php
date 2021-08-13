<?php

namespace Kunlabo\User\Domain\ValueObject;

use Kunlabo\User\Domain\ValueObject\Exception\InvalidPasswordException;
use RuntimeException;

// This isn't a StringValueObject because I value the ::fromHash and ::fromPlain distinction.
// Having ::fromRaw would be weird.
final class HashedPassword
{
    public const ALGO = PASSWORD_BCRYPT;
    public const OPTIONS = ['cost' => 12];

    private function __construct(private string $hash)
    {
    }

    public static function fromPlain(string $plain): self
    {
        self::validatePassword($plain);

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
        return password_needs_rehash($hash, self::ALGO, self::OPTIONS);
    }

    private static function validatePassword(string $plain): void
    {
        if (strlen($plain) < 8 || strlen($plain) > 72) {
            throw new InvalidPasswordException("Password must be between 8 and 72 characters.");
        }

        $uppercase = preg_match('@[A-Z]@', $plain);
        $lowercase = preg_match('@[a-z]@', $plain);
        $number = preg_match('@[0-9]@', $plain);
        $special = preg_match('@[^\w]@', $plain);

        if (!$uppercase || !$lowercase || !$number || !$special) {
            throw new InvalidPasswordException(
                "Password must contain both upper and lowercase letters, a number and a special character."
            );
        }
    }

    public function match(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->hash);
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function equals(HashedPassword $other): bool
    {
        return $this->hash === $other->hash;
    }
}