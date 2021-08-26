<?php

namespace Kunlabo\Participant\Application\Command\UpdateParticipant;

use Kunlabo\Participant\Domain\Exception\UnknownParticipantException;
use Kunlabo\Participant\Domain\ParticipantRepository;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;

final class UpdateParticipantHandler implements CommandHandler
{
    public function __construct(private ParticipantRepository $repository) {
    }

    public function __invoke(UpdateParticipantCommand $command): void
    {
        $participant = $this->repository->readById($command->getId());

        if ($participant === null) {
            throw new UnknownParticipantException($command->getId());
        }

        // Not publishing domain events on purpose (would be too many)
        $participant->update();
        $this->repository->update($participant);
    }
}