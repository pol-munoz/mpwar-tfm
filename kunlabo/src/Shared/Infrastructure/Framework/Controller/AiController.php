<?php

namespace Kunlabo\Shared\Infrastructure\Framework\Controller;

use Kunlabo\Action\Application\Command\NewActions\NewActionsCommand;
use Kunlabo\Participant\Application\Query\FindParticipantById\FindParticipantByIdQuery;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Study\Application\Query\FindStudyById\FindStudyByIdQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AiController extends AbstractController
{
    #[Route('/ai/{id}/{participant}', name: 'web_ai_post', methods: ['POST'])]
    public function aiPost(
        Request $request,
        CommandBus $commandBus,
        QueryBus $queryBus,
        string $id,
        string $participant
    ): Response {
        $study = $queryBus->ask(FindStudyByIdQuery::create($id))->getStudy();

        if ($study === null) {
            throw $this->createNotFoundException();
        }

        $p = $queryBus->ask(FindParticipantByIdQuery::create($participant))->getParticipant();

        if ($p === null) {
            throw $this->createNotFoundException();
        }

        $array = $request->toArray();
        $actions = $array['actions'];
        $body = $array['body'];

        $commandBus->dispatch(NewActionsCommand::create(
            $id,
            $participant,
            'agent',
            'engine',
            $actions,
            $body
        ));

        return new Response();
    }
}