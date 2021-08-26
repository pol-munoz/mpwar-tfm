<?php

namespace Kunlabo\Participant\Domain;

use DateTime;
use Kunlabo\Participant\Domain\Event\ParticipantDeletedEvent;
use Kunlabo\Participant\Domain\Event\ParticipantFilledSurveyEvent;
use Kunlabo\Participant\Domain\ValueObject\Age;
use Kunlabo\Participant\Domain\ValueObject\Gender;
use Kunlabo\Participant\Domain\ValueObject\Handedness;
use Kunlabo\Shared\Domain\Aggregate\AggregateRoot;
use Kunlabo\Shared\Domain\Aggregate\NamedAggregateRoot;
use Kunlabo\Shared\Domain\ValueObject\Name;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class Participant extends NamedAggregateRoot
{
    public function __construct(
        Uuid $id,
        DateTime $created,
        DateTime $modified,
        Name $name,
        private Uuid $studyId,
        private Age $age,
        private Gender $gender,
        private Handedness $handedness
    )
    {
        parent::__construct($id, $created, $modified, $name);
    }

    public static function create(
        Uuid $id,
        Uuid $studyId,
        Name $name,
        Age $age,
        Gender $gender,
        Handedness $handedness
    ): self {

        $participant = new self($id, new DateTime(), new DateTime(), $name, $studyId, $age, $gender, $handedness);
        $participant->record(new ParticipantFilledSurveyEvent($participant));

        return $participant;
    }

    public function getStudyId(): Uuid
    {
        return $this->studyId;
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

    public function delete(): void
    {
        $this->record(new ParticipantDeletedEvent($this));
    }
}