<?php

namespace Kunlabo\Participant\Application\Command\DeleteParticipant;

use Kunlabo\Participant\Domain\Exception\UnknownParticipantException;
use Kunlabo\Participant\Domain\ParticipantRepository;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;

final class DeleteParticipantHandler implements CommandHandler
{
    public function __construct(private DomainEventBus $eventBus, private ParticipantRepository $repository) {
    }

    public function __invoke(DeleteParticipantCommand $command): void
    {
        $participant = $this->repository->readById($command->getId());

        if ($participant === null) {
            throw new UnknownParticipantException($command->getId());
        }

        $participant->delete();
        $this->repository->delete($participant);
        $this->eventBus->publish(...$participant->pullDomainEvents());
    }
}