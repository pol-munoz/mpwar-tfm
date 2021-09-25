<?php

namespace Kunlabo\Action\Application\Event;

use Kunlabo\Action\Infrastructure\FilePersistService;
use Kunlabo\Shared\Application\Bus\Event\DomainEventSubscriber;
use Kunlabo\Study\Domain\Event\StudyDeletedEvent;

final class DeletePersistenceOnStudyDeleted implements DomainEventSubscriber
{
    public function __construct(
        private FilePersistService $service
    ) {
    }

    public function __invoke(StudyDeletedEvent $event): void
    {
        $this->service->deleteActionsForStudyId($event->getAggregateId());
    }
}