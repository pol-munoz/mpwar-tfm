<?php

namespace Kunlabo\Engine\Application\Command\SetEngineMainFile;

use Kunlabo\Engine\Domain\EngineRepository;
use Kunlabo\Engine\Domain\Exception\UnknownEngineException;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;

final class SetEngineMainFileHandler implements CommandHandler
{
    public function __construct(private DomainEventBus $eventBus, private EngineRepository $repository)
    {
    }

    public function __invoke(SetEngineMainFileCommand $command): void
    {
        $engine = $this->repository->readById($command->getEngineId());

        if ($engine === null) {
            throw new UnknownEngineException($command->getEngineId());
        }

        $engine->setMain($command->getPath());

        $this->repository->update($engine);
        $this->eventBus->publish(...$engine->pullDomainEvents());
    }
}