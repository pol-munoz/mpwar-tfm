<?php

namespace Kunlabo\Agent\Domain;

use DateTime;
use Kunlabo\Shared\Domain\Aggregate\Entity;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class AgentFile extends Entity
{
    public const BASE_PATH = 'uploads/agents/';

    private function __construct(
        Uuid $id,
        DateTime $created,
        DateTime $modified,
        private Uuid $agentId,
        private string $path
    ) {
        parent::__construct($id, $created, $modified);
    }

    public static function create(
        Uuid $id,
        Uuid $agentId,
        string $path
    ): self {
        return new self($id, new DateTime(), new DateTime(), $agentId, $path);
    }

    public function getAgentId(): Uuid
    {
        return $this->agentId;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUrl(): string
    {
        return self::BASE_PATH . $this->agentId->getRaw() . $this->path;
    }
}