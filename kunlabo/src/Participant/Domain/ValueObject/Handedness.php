<?php

namespace Kunlabo\Participant\Domain\ValueObject;

use Kunlabo\Participant\Domain\ValueObject\Exception\InvalidHandednessException;
use Kunlabo\Shared\Domain\ValueObject\StringValueObject;

final class Handedness extends StringValueObject
{
    const LEFT = 'left';
    const RIGHT = 'right';
    const AMBIDEXTROUS = 'ambidextrous';
    const HANDEDNESSES = [self::LEFT, self::RIGHT, self::AMBIDEXTROUS];

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

    public function isLeft(): bool
    {
        return $this->raw === self::LEFT;
    }

    public function isRight(): bool
    {
        return $this->raw === self::RIGHT;
    }

    public function isAmbidextrous(): bool
    {
        return $this->raw === self::AMBIDEXTROUS;
    }
}