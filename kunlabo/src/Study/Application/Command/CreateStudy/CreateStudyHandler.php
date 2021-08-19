<?php

namespace Kunlabo\Study\Application\Command\CreateStudy;

use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;
use Kunlabo\Study\Domain\Study;
use Kunlabo\Study\Domain\StudyRepository;

final class CreateStudyHandler implements CommandHandler
{
    public function __construct(private DomainEventBus $eventBus, private StudyRepository $repository) {
    }

    public function __invoke(CreateStudyCommand $command): void
    {
        $study = Study::create($command->getUuid(), $command->getName(), $command->getOwner(), $command->getEngine(), $command->getAgent());

        $this->repository->create($study);

        $this->eventBus->publish(...$study->pullDomainEvents());
    }
}