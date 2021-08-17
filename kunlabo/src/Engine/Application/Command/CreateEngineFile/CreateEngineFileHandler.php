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
        $engine = $this->repository->readById($command->getEngineId());

        if ($engine === null) {
            throw new UnknownEngineException($command->getEngineId());
        }

        $file = $this->repository->readFileByEngineIdAndPath($engine->getId(), $command->getPath());

        if ($file !== null) {
            $engine->updateFile($file);
            $this->repository->updateFile($file);
        } else {
            $file = $engine->addFile($command->getPath());
            $this->repository->createFile($file);
        }

        $this->repository->update($engine);

        $this->eventBus->publish(...$engine->pullDomainEvents());
    }
}