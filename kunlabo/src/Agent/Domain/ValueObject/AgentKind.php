<?php

namespace Kunlabo\Agent\Domain\ValueObject;

use Kunlabo\Agent\Domain\ValueObject\Exception\InvalidAgentKindException;
use Kunlabo\Shared\Domain\ValueObject\StringValueObject;

final class AgentKind extends StringValueObject
{
    const HUMAN_KIND = 'human';
    const AI_KIND = 'ai';

    const KINDS = [self::HUMAN_KIND, self::AI_KIND];

    public static function fromRaw(string $raw): self
    {
        self::assertValidKind($raw);
        return new self($raw);
    }

    private static function assertValidKind(string $kind): void
    {
        if (!in_array($kind, self::KINDS)) {
            throw new InvalidAgentKindException($kind);
        }
    }

    public function getDefaultFile(): string
    {
        switch ($this->raw) {
            case self::HUMAN_KIND:
                return '/index.html';
            case self::AI_KIND:
                return '/main.py';
        }
        return '';
    }
}