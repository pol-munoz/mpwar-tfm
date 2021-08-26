<?php

namespace Kunlabo\Agent\Application\Command\DeleteAgentFile;

use Kunlabo\Agent\Domain\AgentRepository;
use Kunlabo\Agent\Domain\Exception\UnknownAgentException;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;

final class DeleteAgentFileHandler implements CommandHandler
{
    public function __construct(private DomainEventBus $eventBus, private AgentRepository $repository)
    {
    }

    public function __invoke(DeleteAgentFileCommand $command): void
    {
        $agent = $this->repository->readById($command->getAgentId());

        if ($agent === null) {
            throw new UnknownAgentException($command->getAgentId());
        }

        $file = $this->repository->readFileByAgentIdAndPath($agent->getId(), $command->getPath());

        if ($file !== null) {
            $this->repository->deleteFile($file);

            $agent->deleteFile($file);
            $this->repository->update($agent);
            $this->eventBus->publish(...$agent->pullDomainEvents());
        }
    }
}