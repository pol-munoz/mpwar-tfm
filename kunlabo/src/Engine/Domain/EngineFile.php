<?php

namespace Kunlabo\Engine\Domain;

use DateTime;
use Kunlabo\Shared\Domain\Aggregate\Entity;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class EngineFile extends Entity
{
    public const BASE_PATH = 'uploads/engines/';

    private function __construct(
        Uuid $id,
        DateTime $created,
        DateTime $modified,
        private Uuid $engineId,
        private string $path
    ) {
        parent::__construct($id, $created, $modified);
    }

    public static function create(
        Uuid $id,
        Uuid $engineId,
        string $path
    ): self {
        return new self($id, new DateTime(), new DateTime(), $engineId, $path);
    }

    public function getEngineId(): Uuid
    {
        return $this->engineId;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUrl(): string
    {
        return self::BASE_PATH . $this->engineId->getRaw() . $this->path;
    }
}