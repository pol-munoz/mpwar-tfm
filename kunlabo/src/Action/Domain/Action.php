<?php

namespace Kunlabo\Action\Domain;

use DateTime;
use Kunlabo\Shared\Domain\Aggregate\Entity;
use Kunlabo\Action\Domain\ValueObject\ActionKind;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class Action extends Entity
{
    private function __construct(
        Uuid $id,
        DateTime $created,
        DateTime $modified,
        private Uuid $studyId,
        private Uuid $participantId,
        private ActionKind $kind,
        private string $source,
        private string $destination,
        private array $body
    ) {
        parent::__construct($id, $created, $modified);
    }

    public static function create(
        Uuid $studyId,
        Uuid $participantId,
        ActionKind $kind,
        string $source,
        string $destination,
        array $body
    ): self {
        return new self(
            Uuid::random(),
            new DateTime(),
            new DateTime(),
            $studyId,
            $participantId,
            $kind,
            $source,
            $destination,
            $body
        );
    }

    public function getStudyId(): Uuid
    {
        return $this->studyId;
    }

    public function getParticipantId(): Uuid
    {
        return $this->participantId;
    }

    public function getKind(): ActionKind
    {
        return $this->kind;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function getBody(): array
    {
        return $this->body;
    }
}