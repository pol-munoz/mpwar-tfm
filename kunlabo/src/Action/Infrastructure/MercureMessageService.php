<?php

namespace Kunlabo\Action\Infrastructure;

use Kunlabo\Action\Domain\Action;
use Kunlabo\Action\Domain\MessageService;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final class MercureMessageService implements MessageService
{
    public function __construct(private HubInterface $hub)
    {
    }

    public function messageAction(Action $action): void
    {
        $update = new Update(
            'http://kunlabo.com/' . $action->getDestination() .'/' . $action->getStudyId() . '/' . $action->getParticipantId(),
            json_encode($action->getBody())
        );
        $this->hub->publish($update);
    }
}