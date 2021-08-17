<?php

namespace Kunlabo\Engine\Application\Command\CreateEngineFile;

use Kunlabo\Engine\Domain\EngineRepository;
use Kunlabo\Engine\Domain\Exception\UnknownEngineException;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;

final class CreateEngineFileHandler implements CommandHandler
{
    public function __construct(private DomainEventBus $eventBus, private EngineRepository $repository)
    {
    }

    public function __invoke(CreateEngineFileCommand $command): void
    {
        $engine = $this->repository->readById($command->getEngine());

        if (!$engine) {
            throw new UnknownEngineException($command->getEngine());
        }

        $file = $engine->addFile($command->getPath());

        $this->repository->update($engine);
        $this->repository->createFile($file);

        $this->eventBus->publish(...$engine->pullDomainEvents());
    }
}