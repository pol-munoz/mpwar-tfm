<?php

namespace Kunlabo\Log\Domain;

use DateTime;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class Log
{
    const TYPE_KEY = 'type';

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
        return  $this->created->getTimestamp() * 1000 + (intval($this->created->format('u')) / 1000);
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

    public function hasType(): bool
    {
        return array_key_exists(self::TYPE_KEY, $this->body);
    }

    public function getType(): string
    {
        return $this->body[self::TYPE_KEY];
    }
}