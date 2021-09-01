<?php

namespace Kunlabo\Study\Infrastructure\Framework\Controller;

use DomainException;
use Kunlabo\Action\Application\Command\NewActions\NewActionsCommand;
use Kunlabo\Agent\Application\Query\FindAgentById\FindAgentByIdQuery;
use Kunlabo\Participant\Application\Query\FindParticipantById\FindParticipantByIdQuery;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Study\Application\Query\FindStudyById\FindStudyByIdQuery;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HumanStudyController extends AbstractController
{
    #[Route('/{id}/{participant}', name: 'web_studies_human', methods: ['GET'])]
    public function human(
        QueryBus $queryBus,
        string $id,
        string $participant
    ): Response {
        try {
            $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

            $study = $queryBus->ask(FindStudyByIdQuery::create($id))->getStudy();

            if ($study === null) {
                throw $this->createNotFoundException();
            }

            $p = $queryBus->ask(FindParticipantByIdQuery::create($participant))->getParticipant();

            if ($p === null) {
                throw $this->createNotFoundException();
            }

            $agent = $queryBus->ask(FindAgentByIdQuery::create($study->getAgentId()))->getAgent();
            $human = $agent->getKind()->isHuman();

            if (!$human) {
                throw $this->createNotFoundException();
            }

            return $this->render(
                'app/studies/human.html.twig',
                ['study' => $study, 'agent' => $agent, 'participant' => $p]
            );
        } catch (DomainException) {
            throw $this->createNotFoundException();
        }
    }

    #[Route('/{id}/{participant}', name: 'web_studies_human_post', methods: ['POST'])]
    public function humanPost(
        Request $request,
        CommandBus $commandBus,
        QueryBus $queryBus,
        string $id,
        string $participant
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

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