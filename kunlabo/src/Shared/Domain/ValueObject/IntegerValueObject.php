<?php

namespace Kunlabo\Shared\Domain\ValueObject;

abstract class IntegerValueObject
{
    protected function __construct(protected int $raw)
    {
    }

    abstract public static function fromRaw(int $raw): self;

    public function getRaw(): int
    {
        return $this->raw;
    }

    public function equals(IntegerValueObject $other): bool
    {
        return get_class($this) === get_class($other) && $this->raw === $other->raw;
    }
}