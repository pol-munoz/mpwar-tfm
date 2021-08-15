<?php

namespace Kunlabo\User\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Kunlabo\User\Domain\ValueObject\Role;

// IMO this is a good compromise, as having a fake entity to represent roles as a one to many relationship is overkill,
// embeddables don't support collections, json would require extra serialization in the Domain layer
// and simple_array (or json) wouldn't respect the Value Object
final class RoleArrayType extends StringType
{
    public const NAME = 'role_array';

    private const SEPARATOR = ',';

    public function convertToPHPValue($value, AbstractPlatform $platform): array
    {
        return array_map(
            function ($role) {
                return Role::fromRaw($role);
            },
            explode(self::SEPARATOR, $value)
        );
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        $names = array_map(
            function ($role) {
                return $role->getRaw();
            },
            $value
        );
        return implode(self::SEPARATOR, $names);
    }

    public function getName()
    {
        return self::NAME;
    }
}