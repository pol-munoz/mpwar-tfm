<?php

namespace Kunlabo\Study\Application\Command\DeleteStudy;

use Kunlabo\Agent\Application\Query\FindAgentById\FindAgentByIdQuery;
use Kunlabo\Agent\Domain\Exception\UnknownAgentException;
use Kunlabo\Engine\Application\Query\FindEngineById\FindEngineByIdQuery;
use Kunlabo\Engine\Domain\Exception\UnknownEngineException;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;
use Kunlabo\Study\Domain\Exception\UnknownStudyException;
use Kunlabo\Study\Domain\Study;
use Kunlabo\Study\Domain\StudyRepository;

final class DeleteStudyHandler implements CommandHandler
{
    public function __construct(
        private DomainEventBus $eventBus,
        private StudyRepository $repository
    ) {
    }

    public function __invoke(DeleteStudyCommand $command): void
    {
        $study = $this->repository->readById($command->getId());

        if ($study === null) {
            throw new UnknownStudyException($command->getId());
        }

        $study->delete();
        $this->repository->delete($study);

        $this->eventBus->publish(...$study->pullDomainEvents());
    }
}