<?php

namespace Kunlabo\Log\Application\Event;

use Kunlabo\Log\Domain\LogRepository;
use Kunlabo\Participant\Domain\Event\ParticipantDeletedEvent;
use Kunlabo\Shared\Application\Bus\Event\DomainEventSubscriber;

final class DeleteLogsOnParticipantDeleted implements DomainEventSubscriber
{
    public function __construct(
        private LogRepository $repository
    ) {
    }

    public function __invoke(ParticipantDeletedEvent $event): void
    {
        $this->repository->deleteAllByParticipantId($event->getAggregateId());
    }
}