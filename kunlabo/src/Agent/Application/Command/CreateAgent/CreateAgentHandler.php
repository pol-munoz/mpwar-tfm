<?php

namespace Kunlabo\Agent\Application\Command\CreateAgent;

use Kunlabo\Agent\Domain\Agent;
use Kunlabo\Agent\Domain\AgentRepository;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;

final class CreateAgentHandler implements CommandHandler
{
    public function __construct(private DomainEventBus $eventBus, private AgentRepository $repository) {
    }

    public function __invoke(CreateAgentCommand $command): void
    {
        $agent = Agent::create($command->getUuid(), $command->getName(), $command->getOwner(), $command->getKind());

        $this->repository->create($agent);

        $this->eventBus->publish(...$agent->pullDomainEvents());
    }
}