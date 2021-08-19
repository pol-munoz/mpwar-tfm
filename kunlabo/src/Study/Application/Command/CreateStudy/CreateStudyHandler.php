<?php

namespace Kunlabo\Study\Application\Command\CreateStudy;

use Kunlabo\Agent\Application\Query\FindAgentById\FindAgentByIdQuery;
use Kunlabo\Agent\Domain\Exception\UnknownAgentException;
use Kunlabo\Engine\Application\Query\FindEngineById\FindEngineByIdQuery;
use Kunlabo\Engine\Domain\Exception\UnknownEngineException;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Study\Domain\Study;
use Kunlabo\Study\Domain\StudyRepository;

final class CreateStudyHandler implements CommandHandler
{
    public function __construct(
        private DomainEventBus $eventBus,
        private StudyRepository $repository,
        private QueryBus $queryBus
    ) {
    }

    public function __invoke(CreateStudyCommand $command): void
    {
        $engine = $this->queryBus->ask(FindEngineByIdQuery::fromId($command->getEngineId()))->getEngine();
        if ($engine === null) {
            throw new UnknownEngineException($command->getEngineId());
        }

        $agent = $this->queryBus->ask(FindAgentByIdQuery::fromId($command->getAgentId()))->getAgent();
        if ($agent === null) {
            throw new UnknownAgentException($command->getEngineId());
        }

        $study = Study::create(
            $command->getUuid(),
            $command->getName(),
            $command->getOwner(),
            $command->getEngineId(),
            $command->getAgentId()
        );

        $this->repository->create($study);

        $this->eventBus->publish(...$study->pullDomainEvents());
    }
}