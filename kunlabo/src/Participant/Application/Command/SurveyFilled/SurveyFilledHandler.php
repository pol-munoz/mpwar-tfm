<?php

namespace Kunlabo\Participant\Application\Command\SurveyFilled;

use Kunlabo\Participant\Domain\Exception\ParticipantAlreadyExistsException;
use Kunlabo\Participant\Domain\Participant;
use Kunlabo\Participant\Domain\ParticipantRepository;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;

final class SurveyFilledHandler implements CommandHandler
{
    public function __construct(private DomainEventBus $eventBus, private ParticipantRepository $repository)
    {
    }

    public function __invoke(SurveyFilledCommand $command): void
    {
        if ($this->repository->readById($command->getUuid()) !== null) {
            throw new ParticipantAlreadyExistsException();
        }

        $participant = Participant::create(
            $command->getUuid(),
            $command->getSurveyId(),
            $command->getNickname(),
            $command->getAge(),
            $command->getGender(),
            $command->getHandedness()
        );

        $this->repository->create($participant);

        $this->eventBus->publish(...$participant->pullDomainEvents());
    }
}
