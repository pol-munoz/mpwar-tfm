<?php

namespace Kunlabo\Engine\Application\Command\DeleteEngine;

use Kunlabo\Engine\Domain\Engine;
use Kunlabo\Engine\Domain\EngineRepository;
use Kunlabo\Engine\Domain\Exception\UnknownEngineException;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;

final class DeleteEngineHandler implements CommandHandler
{
    public function __construct(private DomainEventBus $eventBus, private EngineRepository $repository) {
    }

    public function __invoke(DeleteEngineCommand $command): void
    {
        $engine = $this->repository->readById($command->getId());

        if ($engine === null) {
            throw new UnknownEngineException($command->getId());
        }

        $files = $this->repository->readFilesForEngineId($engine->getId());

        foreach ($files as $file) {
            $engine->deleteFile($file);
            $this->repository->deleteFile($file);
        }

        $engine->delete();
        $this->repository->delete($engine);
        $this->eventBus->publish(...$engine->pullDomainEvents());
    }
}