<?php

namespace Kunlabo\Action\Domain\ValueObject;

use Kunlabo\Action\Domain\ValueObject\Exception\InvalidActionException;
use Kunlabo\Shared\Domain\ValueObject\StringValueObject;

final class ActionKind extends StringValueObject
{
    const MESSAGE = 'MESSAGE';
    const LOG = 'LOG';
    const PERSIST = 'PERSIST';
    const ACTIONS = [self::MESSAGE, self::LOG, self::PERSIST];

    public static function fromRaw(string $raw): self
    {
        self::assertValidAction($raw);
        return new self($raw);
    }

    private static function assertValidAction(string $action): void
    {
        if (!in_array($action, self::ACTIONS)) {
            throw new InvalidActionException($action);
        }
    }

    public function isMessage(): bool
    {
        return $this->raw === self::MESSAGE;
    }

    public function isLog(): bool
    {
        return $this->raw === self::LOG;
    }

    public function isPersist(): bool
    {
        return $this->raw === self::PERSIST;
    }
}