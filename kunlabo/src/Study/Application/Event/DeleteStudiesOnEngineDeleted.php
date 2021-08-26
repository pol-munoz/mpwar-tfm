<?php

namespace Kunlabo\Study\Application\Event;

use Kunlabo\Engine\Domain\Event\EngineDeletedEvent;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;
use Kunlabo\Shared\Application\Bus\Event\DomainEventSubscriber;
use Kunlabo\Study\Domain\StudyRepository;

final class DeleteStudiesOnEngineDeleted implements DomainEventSubscriber
{
    public function __construct(
        private DomainEventBus $eventBus,
        private StudyRepository $repository
    ) {
    }

    public function __invoke(EngineDeletedEvent $event): void
    {
        $studies = $this->repository->readAllByEngineId($event->getAggregateId());

        foreach ($studies as $study) {
            $study->delete();
            $this->repository->delete($study);

            $this->eventBus->publish(...$study->pullDomainEvents());
        }
    }
}