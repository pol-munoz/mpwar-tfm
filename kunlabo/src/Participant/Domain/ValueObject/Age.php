<?php

namespace Kunlabo\Participant\Domain\ValueObject;

use Kunlabo\Participant\Domain\ValueObject\Exception\InvalidAgeException;
use Kunlabo\Shared\Domain\ValueObject\IntegerValueObject;

final class Age extends IntegerValueObject
{
    public static function fromRaw(int $raw): IntegerValueObject
    {
        self::assertValidAge($raw);
        return new self($raw);
    }

    private static function assertValidAge(int $age): void
    {
        if ($age <= 0 || $age >= 150) {
            throw new InvalidAgeException($age);
        }
    }
}