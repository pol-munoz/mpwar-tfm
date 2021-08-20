<?php

namespace Kunlabo\Participant\Domain\ValueObject;

use Kunlabo\Participant\Domain\ValueObject\Exception\InvalidHandednessException;
use Kunlabo\Shared\Domain\ValueObject\StringValueObject;

final class Handedness extends StringValueObject
{
    const HANDEDNESSES = ['left', 'right', 'ambidextrous'];

    public static function fromRaw(string $raw): self
    {
        self::assertValidHandedness($raw);
        return new self($raw);
    }

    private static function assertValidHandedness(string $handedness): void
    {
        if (!in_array($handedness, self::HANDEDNESSES)) {
            throw new InvalidHandednessException($handedness);
        }
    }
}