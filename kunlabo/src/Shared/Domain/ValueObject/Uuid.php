<?php

namespace Kunlabo\Shared\Domain\ValueObject;

use Kunlabo\Shared\Domain\ValueObject\Exception\InvalidUuidException;
use Ramsey\Uuid\Uuid as RamseyUuid;

final class Uuid extends StringValueObject
{
    public static function fromRaw(string $raw): self
    {
        self::assertValidId($raw);
        return new self($raw);
    }

    public static function random(): self
    {
        return new self(RamseyUuid::uuid4()->toString());
    }

    private static function assertValidId(string $id): void
    {
        if (!RamseyUuid::isValid($id)) {
            throw new InvalidUuidException($id);
        }
    }

    public function equals(Uuid $other): bool
    {
        return $this->raw === $other->raw;
    }
}
