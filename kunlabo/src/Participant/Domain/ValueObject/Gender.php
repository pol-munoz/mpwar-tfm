<?php

namespace Kunlabo\Participant\Domain\ValueObject;

use Kunlabo\Participant\Domain\ValueObject\Exception\InvalidGenderException;
use Kunlabo\Shared\Domain\ValueObject\StringValueObject;

final class Gender extends StringValueObject
{
    const GENDERS = ['male', 'female', 'nb'];

    public static function fromRaw(string $raw): self
    {
        self::assertValidGender($raw);
        return new self($raw);
    }

    private static function assertValidGender(string $gender): void
    {
        if (!in_array($gender, self::GENDERS)) {
            throw new InvalidGenderException($gender);
        }
    }
}