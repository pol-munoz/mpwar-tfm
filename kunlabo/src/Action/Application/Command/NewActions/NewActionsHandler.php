<?php

namespace Kunlabo\Action\Application\Command\NewActions;

use Kunlabo\Action\Domain\Action;
use Kunlabo\Action\Domain\LogService;
use Kunlabo\Action\Domain\MessageService;
use Kunlabo\Shared\Application\Bus\Command\CommandHandler;

final class NewActionsHandler implements CommandHandler
{
    public function __construct(private MessageService $message, private LogService $log)
    {
    }

    public function __invoke(NewActionsCommand $command): void
    {
        $kinds = $command->getKinds();

        foreach ($kinds as $kind) {
            $action = Action::create(
                $command->getStudyId(),
                $command->getParticipantId(),
                $kind,
                $command->getSource(),
                $command->getDestination(),
                $command->getBody(),
                $command->getExtras()
            );

            if ($kind->isMessage()) {
                $this->message->messageAction($action);
            } else if ($kind->isLog()) {
                $this->log->logAction($action);
            }
        }
    }
}