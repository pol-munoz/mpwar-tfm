<?php

namespace Kunlabo\Engine\Application\Command\DeleteEngineFile;

use Kunlabo\Engine\Domain\EngineRepository;
use Kunlabo\Engine\Domain\Exception\UnknownEngineException;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;

final class DeleteEngineFileHandler implements CommandHandler
{
    public function __construct(private DomainEventBus $eventBus, private EngineRepository $repository)
    {
    }

    public function __invoke(DeleteEngineFileCommand $command): void
    {
        $engine = $this->repository->readById($command->getEngineId());

        if ($engine === null) {
            throw new UnknownEngineException($command->getEngineId());
        }

        $file = $this->repository->readFileByEngineIdAndPath($engine->getId(), $command->getPath());

        if ($file !== null) {
            $this->repository->deleteFile($file);

            $engine->deleteFile($file);
            $this->repository->update($engine);
            $this->eventBus->publish(...$engine->pullDomainEvents());
        }
    }
}