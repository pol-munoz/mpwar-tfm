<?php

namespace Kunlabo\Participant\Domain\ValueObject;

use Kunlabo\Participant\Domain\ValueObject\Exception\InvalidGenderException;
use Kunlabo\Shared\Domain\ValueObject\StringValueObject;

final class Gender extends StringValueObject
{
    const MALE = 'male';
    const FEMALE = 'female';
    const NONBINARY = 'nb';
    const GENDERS = [self::MALE, self::FEMALE, self::NONBINARY];

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

    public function isMale(): bool
    {
        return $this->raw === self::MALE;
    }

    public function isFemale(): bool
    {
        return $this->raw === self::FEMALE;
    }

    public function isNonBinary(): bool
    {
        return $this->raw === self::NONBINARY;
    }
}