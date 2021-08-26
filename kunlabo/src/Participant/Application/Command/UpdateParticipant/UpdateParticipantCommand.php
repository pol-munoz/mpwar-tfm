<?php

namespace Kunlabo\Participant\Application\Command\UpdateParticipant;

use Kunlabo\Shared\Application\Bus\Command\Command;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class UpdateParticipantCommand implements Command
{
    private function __construct(
        private Uuid $id,
        private Uuid $surveyId
    ) {
    }

    public static function create(
        string $id,
        string $surveyId,
    ): self {
        return new self(
            Uuid::fromRaw($id),
            Uuid::fromRaw($surveyId),
        );
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getSurveyId(): Uuid
    {
        return $this->surveyId;
    }
}