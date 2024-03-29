<?php

namespace Kunlabo\Participant\Infrastructure\Framework\Controller;

use Kunlabo\Action\Application\Command\NewActions\NewActionsCommand;
use Kunlabo\Agent\Application\Query\FindAgentById\FindAgentByIdQuery;
use Kunlabo\Engine\Application\Query\FindEngineById\FindEngineByIdQuery;
use Kunlabo\Participant\Application\Command\UpdateParticipant\UpdateParticipantCommand;
use Kunlabo\Participant\Application\Query\FindParticipantById\FindParticipantByIdQuery;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Study\Application\Query\FindStudyById\FindStudyByIdQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ParticipantController extends AbstractController
{
    public const STUDIES_SESSION_KEY = 'studies';

    #[Route('/{id}', name: 'web_participant', methods: ['GET'])]
    public function participant(
        QueryBus $queryBus,
        SessionInterface $session,
        UrlGeneratorInterface $urlGenerator,
        string $id
    ): Response {
        $study = $queryBus->ask(FindStudyByIdQuery::create($id))->getStudy();

        if ($study === null) {
            throw $this->createNotFoundException();
        }

        if (!$session->has(self::STUDIES_SESSION_KEY) ||
            !array_key_exists($id, $session->get(self::STUDIES_SESSION_KEY))) {
            return new RedirectResponse(
                $urlGenerator->generate('web_participant_survey', ['id' => $id]), Response::HTTP_SEE_OTHER
            );
        }
        $participant = $session->get(self::STUDIES_SESSION_KEY)[$id];

        $p = $queryBus->ask(FindParticipantByIdQuery::create($participant))->getParticipant();
        if ($p === null) {
            return new RedirectResponse(
                $urlGenerator->generate('web_participant_survey', ['id' => $id]), Response::HTTP_SEE_OTHER
            );
        }

        $engine = $queryBus->ask(FindEngineByIdQuery::create($study->getEngineId()))->getEngine();

        return $this->render(
            'study/study.html.twig',
            ['study' => $study, 'engine' => $engine, 'participant' => $participant]
        );
    }

    #[Route('/{id}', name: 'web_participant_post', methods: ['POST'])]
    public function participantPost(
        Request $request,
        CommandBus $commandBus,
        QueryBus $queryBus,
        SessionInterface $session,
        string $id
    ): Response {
        $study = $queryBus->ask(FindStudyByIdQuery::create($id))->getStudy();

        if ($study === null) {
            throw $this->createNotFoundException();
        }

        if (!$session->has(self::STUDIES_SESSION_KEY) ||
            !array_key_exists($id, $session->get(self::STUDIES_SESSION_KEY))) {
            return new Response('No participant', Response::HTTP_FORBIDDEN);
        }

        $participant = $session->get(self::STUDIES_SESSION_KEY)[$id];

        $p = $queryBus->ask(FindParticipantByIdQuery::create($participant))->getParticipant();
        if ($p === null) {
            return new Response('No participant', Response::HTTP_FORBIDDEN);
        }

        $commandBus->dispatch(UpdateParticipantCommand::create($participant, $id));

        $agent = $queryBus->ask(FindAgentByIdQuery::create($study->getAgentId()))->getAgent();

        $array = $request->toArray();
        $actions = $array['actions'];
        $body = $array['body'];

        if ($agent->getKind()->isHuman()) {
            $commandBus->dispatch(
                NewActionsCommand::create(
                    $id,
                    $participant,
                    'engine',
                    'agent',
                    $actions,
                    $body
                )
            );
        } else {
            $commandBus->dispatch(
                NewActionsCommand::create(
                    $id,
                    $participant,
                    'engine',
                    'agent',
                    $actions,
                    $body,
                    [
                        'study' => $id,
                        'participant' => $participant,
                        'agent' => $agent->getId()->getRaw(),
                        'file' => $agent->getMain(),
                    ]
                )
            );
        }

        return new Response();
    }
}