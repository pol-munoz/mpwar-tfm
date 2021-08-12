<?php

namespace Kunlabo\User\Domain\ValueObject;

use Kunlabo\Shared\Domain\ValueObject\StringValueObject;
use Kunlabo\User\Domain\ValueObject\Exception\InvalidRoleException;

final class Role extends StringValueObject
{
    public const ROLE_USER = 'user';
    public const ROLE_RESEARCHER = 'researcher';
    private const ROLES = [self::ROLE_USER, self::ROLE_RESEARCHER];

    public static function fromRaw(string $raw): self
    {
        $raw = strtolower($raw);
        self::assertValidRole($raw);
        return new self($raw);
    }

    public static function createUserRole()
    {
        return new self(self::ROLE_USER);
    }

    private static function assertValidRole(string $role): void
    {
        if (!in_array($role, self::ROLES)) {
            throw new InvalidRoleException($role);
        }
    }
}