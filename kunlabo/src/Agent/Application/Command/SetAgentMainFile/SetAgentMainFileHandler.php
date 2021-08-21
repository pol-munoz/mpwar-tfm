<?php

namespace Kunlabo\Agent\Application\Command\SetAgentMainFile;

use Kunlabo\Agent\Domain\AgentRepository;
use Kunlabo\Agent\Domain\Exception\UnknownAgentException;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;

final class SetAgentMainFileHandler implements CommandHandler
{
    public function __construct(private DomainEventBus $eventBus, private AgentRepository $repository)
    {
    }

    public function __invoke(SetAgentMainFileCommand $command): void
    {
        $agent = $this->repository->readById($command->getAgentId());

        if ($agent === null) {
            throw new UnknownAgentException($command->getAgentId());
        }

        $agent->setMain($command->getPath());

        $this->repository->update($agent);
        $this->eventBus->publish(...$agent->pullDomainEvents());
    }
}