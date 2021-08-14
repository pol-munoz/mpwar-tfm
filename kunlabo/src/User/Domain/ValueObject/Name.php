<?php

namespace Kunlabo\User\Domain\ValueObject;

use Kunlabo\Shared\Domain\ValueObject\StringValueObject;
use Kunlabo\User\Domain\ValueObject\Exception\InvalidNameException;

final class Name extends StringValueObject
{
    public static function fromRaw(string $raw): Name
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