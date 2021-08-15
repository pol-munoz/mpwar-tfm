<?php

namespace Kunlabo\Shared\Domain\ValueObject;

use Kunlabo\Shared\Domain\ValueObject\Exception\InvalidNameException;

final class Name extends StringValueObject
{
    public static function fromRaw(string $raw): self
    {
        self::assertValidName($raw);
        return new self($raw);
    }

    private static function assertValidName(string $name):void
    {
        if (empty($name)) {
            throw new InvalidNameException("Please enter a name");
        }

        if (strlen($name) > 255) {
            throw new InvalidNameException("Names must be shorter than 255 characters");
        }
    }
}