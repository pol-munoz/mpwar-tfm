<?php

namespace Kunlabo\Action\Infrastructure;

use Kunlabo\Action\Domain\Action;
use Kunlabo\Action\Domain\LogService;
use Psr\Log\LoggerInterface;

final class MonologLogService implements LogService
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $kunlaboLogger)
    {
        $this->logger = $kunlaboLogger;
    }

    public function logAction(Action $action): void
    {
        $this->logger->info(
            'An action was logged for participant ' . $action->getStudyId() . ' in study ' . $action->getStudyId(),
            [
                'study' => $action->getStudyId()->getRaw(),
                'participant' => $action->getParticipantId()->getRaw(),
                'body' => $action->getBody()
            ]
        );
    }
}