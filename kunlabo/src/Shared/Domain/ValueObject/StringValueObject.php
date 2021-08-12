<?php

namespace Kunlabo\Shared\Domain\ValueObject;

use Stringable;

abstract class StringValueObject implements Stringable
{
    protected function __construct(protected string $raw)
    {
    }

    abstract public static function fromRaw(string $raw): self;

    public function getRaw(): string
    {
        return $this->raw;
    }

    public function __toString(): string
    {
        return $this->raw;
    }
}