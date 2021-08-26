<?php

namespace Kunlabo\Study\Application\Event;

use Kunlabo\Agent\Domain\Event\AgentDeletedEvent;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;
use Kunlabo\Shared\Application\Bus\Event\DomainEventSubscriber;
use Kunlabo\Study\Domain\StudyRepository;

final class DeleteStudiesOnAgentDeleted implements DomainEventSubscriber
{
    public function __construct(
        private DomainEventBus $eventBus,
        private StudyRepository $repository
    ) {
    }

    public function __invoke(AgentDeletedEvent $event): void
    {
        $studies = $this->repository->readAllByAgentId($event->getAggregateId());

        foreach ($studies as $study) {
            $study->delete();
            $this->repository->delete($study);

            $this->eventBus->publish(...$study->pullDomainEvents());
        }
    }
}