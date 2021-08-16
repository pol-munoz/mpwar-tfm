<?php

namespace Kunlabo\Engine\Application\Command\CreateEngine;

use Kunlabo\Engine\Domain\Engine;
use Kunlabo\Engine\Domain\EngineRepository;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;

final class CreateEngineHandler implements CommandHandler
{
    public function __construct(private DomainEventBus $eventBus, private EngineRepository $repository) {
    }

    public function __invoke(CreateEngineCommand $command): void
    {
        $engine = Engine::create($command->getUuid(), $command->getName(), $command->getOwner());

        $this->repository->create($engine);

        $this->eventBus->publish(...$engine->pullDomainEvents());
    }
}