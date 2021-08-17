<?php

namespace Kunlabo\Shared\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class EntityUuidType extends GuidType
{
    public const NAME = 'entity_uuid';

    public function convertToPHPValue($value, AbstractPlatform $platform): Uuid
    {
        return Uuid::fromRaw($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value->getRaw();
    }

    public function getName()
    {
        return self::NAME;
    }
}