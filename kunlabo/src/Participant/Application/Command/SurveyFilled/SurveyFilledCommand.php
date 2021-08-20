<?php

namespace Kunlabo\Participant\Application\Command\SurveyFilled;

use Kunlabo\Participant\Domain\ValueObject\Age;
use Kunlabo\Participant\Domain\ValueObject\Gender;
use Kunlabo\Participant\Domain\ValueObject\Handedness;
use Kunlabo\Shared\Application\Bus\Command\Command;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class SurveyFilledCommand implements Command
{
    private function __construct(
        private Uuid $uuid,
        private Uuid $surveyId,
        private Age $age,
        private Gender $gender,
        private Handedness $handedness,
    ) {
    }

    public static function create(
        Uuid $uuid,
        string $surveyId,
        int $age,
        string $gender,
        string $handedness
    ): self {
        return new self(
            $uuid,
            Uuid::fromRaw($surveyId),
            Age::fromRaw($age),
            Gender::fromRaw($gender),
            Handedness::fromRaw($handedness)
        );
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getSurveyId(): Uuid
    {
        return $this->surveyId;
    }

    public function getAge(): Age
    {
        return $this->age;
    }

    public function getGender(): Gender
    {
        return $this->gender;
    }

    public function getHandedness(): Handedness
    {
        return $this->handedness;
    }
}
