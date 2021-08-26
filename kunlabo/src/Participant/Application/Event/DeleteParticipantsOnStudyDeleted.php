<?php

namespace Kunlabo\Participant\Application\Event;

use Kunlabo\Participant\Domain\ParticipantRepository;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;
use Kunlabo\Shared\Application\Bus\Event\DomainEventSubscriber;
use Kunlabo\Study\Domain\Event\StudyDeletedEvent;

final class DeleteParticipantsOnStudyDeleted implements DomainEventSubscriber
{
    public function __construct(
        private DomainEventBus $eventBus,
        private ParticipantRepository $repository
    ) {
    }

    public function __invoke(StudyDeletedEvent $event): void
    {
        $participants = $this->repository->readAllForStudy($event->getAggregateId());

        foreach ($participants as $participant) {
            $participant->delete();
            $this->repository->delete($participant);

            $this->eventBus->publish(...$participant->pullDomainEvents());
        }
    }
}