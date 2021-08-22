<?php

namespace Kunlabo\Action\Application\Command\NewActions;

use Kunlabo\Action\Domain\ValueObject\ActionKind;
use Kunlabo\Shared\Application\Bus\Command\Command;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class NewActionsCommand implements Command
{
    private function __construct(
        private Uuid $studyId,
        private Uuid $participantId,
        private string $source,
        private string $destination,
        private array $kinds,
        private array $body
    ) {
    }

    public static function create(
        string $study,
        string $participant,
        string $source,
        string $destination,
        array $actions,
        array $body
    ): self {
        $kinds = [];

        foreach ($actions as $action) {
            $kinds[] = ActionKind::fromRaw($action);
        }

        return new self(
            Uuid::fromRaw($study),
            Uuid::fromRaw($participant),
            $source,
            $destination,
            $kinds,
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

    public function getSource(): string
    {
        return $this->source;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function getKinds(): array
    {
        return $this->kinds;
    }

    public function getBody(): array
    {
        return $this->body;
    }
}