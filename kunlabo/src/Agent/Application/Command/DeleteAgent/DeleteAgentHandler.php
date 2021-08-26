<?php

namespace Kunlabo\Agent\Application\Command\DeleteAgent;

use Kunlabo\Agent\Domain\Agent;
use Kunlabo\Agent\Domain\AgentRepository;
use Kunlabo\Agent\Domain\Exception\UnknownAgentException;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;

final class DeleteAgentHandler implements CommandHandler
{
    public function __construct(private DomainEventBus $eventBus, private AgentRepository $repository) {
    }

    public function __invoke(DeleteAgentCommand $command): void
    {
        $agent = $this->repository->readById($command->getId());

        if ($agent === null) {
            throw new UnknownAgentException($command->getId());
        }

        $files = $this->repository->readFilesForAgentId($agent->getId());

        foreach ($files as $file) {
            $agent->deleteFile($file);
            $this->repository->deleteFile($file);
        }

        $agent->delete();
        $this->repository->delete($agent);
        $this->eventBus->publish(...$agent->pullDomainEvents());
    }
}