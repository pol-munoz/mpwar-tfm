<?php

namespace Kunlabo\Log\Domain;

use DateTime;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class Log
{
    private function __construct(
        private DateTime $created,
        private Uuid $studyId,
        private Uuid $participantId,
        private array $body
    ) {
    }

    public static function create(
        string $created,
        string $studyId,
        string $participantId,
        array $body,
    ): self
    {
        return new self(
            new DateTime($created),
            Uuid::fromRaw($studyId),
            Uuid::fromRaw($participantId),
            $body
        );
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function getTimestamp(): int
    {
        return  $this->created->getTimestamp();
    }

    public function getStudyId(): Uuid
    {
        return $this->studyId;
    }

    public function getParticipantId(): Uuid
    {
        return $this->participantId;
    }

    public function getBody(): array
    {
        return $this->body;
    }
}