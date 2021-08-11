<?php

namespace Kunlabo\Shared\Domain\ValueObject;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Stringable;

final class Uuid implements Stringable
{
    private function __construct(private string $raw)
    {
    }

    public static function create(string $raw)
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
            throw new InvalidArgumentException('Invalid UUID: ' . $id);
        }
    }

    public function getRaw(): string
    {
        return $this->raw;
    }

    public function equals(Uuid $other): bool
    {
        return $this->raw === $other->raw;
    }

    public function __toString(): string
    {
        return $this->raw;
    }
}
