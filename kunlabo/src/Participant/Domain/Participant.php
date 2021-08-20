<?php

namespace Kunlabo\Participant\Domain;

use DateTime;
use Kunlabo\Participant\Domain\Event\ParticipantFilledSurveyEvent;
use Kunlabo\Participant\Domain\ValueObject\Age;
use Kunlabo\Participant\Domain\ValueObject\Gender;
use Kunlabo\Participant\Domain\ValueObject\Handedness;
use Kunlabo\Shared\Domain\Aggregate\AggregateRoot;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class Participant extends AggregateRoot
{
    public function __construct(
        Uuid $id,
        DateTime $created,
        DateTime $modified,
        private Uuid $studyId,
        private Age $age,
        private Gender $gender,
        private Handedness $handedness
    )
    {
        parent::__construct($id, $created, $modified);
    }

    public static function create(
        Uuid $id,
        Uuid $studyId,
        Age $age,
        Gender $gender,
        Handedness $handedness
    ): self {

        $participant = new self($id, new DateTime(), new DateTime(), $studyId, $age, $gender, $handedness);
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
}