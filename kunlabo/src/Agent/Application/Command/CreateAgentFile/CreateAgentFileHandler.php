<?php

namespace Kunlabo\Agent\Application\Command\CreateAgentFile;

use Kunlabo\Agent\Domain\AgentRepository;
use Kunlabo\Agent\Domain\Exception\UnknownAgentException;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;

final class CreateAgentFileHandler implements CommandHandler
{
    public function __construct(private DomainEventBus $eventBus, private AgentRepository $repository)
    {
    }

    public function __invoke(CreateAgentFileCommand $command): void
    {
        $agent = $this->repository->readById($command->getAgentId());

        if ($agent === null) {
            throw new UnknownAgentException($command->getAgentId());
        }

        $file = $this->repository->readFileByAgentIdAndPath($agent->getId(), $command->getPath());

        if ($file !== null) {
            $agent->updateFile($file);
            $this->repository->updateFile($file);
        } else {
            $file = $agent->addFile($command->getPath());
            $this->repository->createFile($file);
        }
        $this->repository->update($agent);
        $this->eventBus->publish(...$agent->pullDomainEvents());
    }
}