<?php

namespace Kunlabo\User\Domain\ValueObject;

use Kunlabo\Shared\Domain\ValueObject\StringValueObject;
use Kunlabo\User\Domain\ValueObject\Exception\InvalidEmailException;

final class Email extends StringValueObject
{
    public static function fromRaw(string $raw): self
    {
        self::assertValidEmail($raw);
        return new self($raw);
    }

    private static function assertValidEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($email);
        }
    }
}