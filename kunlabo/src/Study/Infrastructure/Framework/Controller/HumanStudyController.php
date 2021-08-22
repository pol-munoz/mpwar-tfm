<?php

namespace Kunlabo\Study\Infrastructure\Framework\Controller;

use DomainException;
use Kunlabo\Agent\Application\Query\FindAgentById\FindAgentByIdQuery;
use Kunlabo\Participant\Application\Query\FindParticipantById\FindParticipantByIdQuery;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\Study\Application\Query\FindStudyById\FindStudyByIdQuery;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

final class HumanStudyController extends AbstractController
{
    #[Route('/human/{id}/{participant}', name: 'web_studies_human', methods: ['GET'])]
    public function human(
        QueryBus $queryBus,
        string $id,
        string $participant
    ): Response {
        try {
            $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

            $study = $queryBus->ask(FindStudyByIdQuery::fromId(Uuid::fromRaw($id)))->getStudy();

            if ($study === null) {
                throw $this->createNotFoundException();
            }

            $p = $queryBus->ask(FindParticipantByIdQuery::create($participant))->getParticipant();

            if ($p === null) {
                throw $this->createNotFoundException();
            }

            $agent = $queryBus->ask(FindAgentByIdQuery::fromId($study->getAgentId()))->getAgent();
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

    #[Route('/human/{id}/{participant}', name: 'web_studies_human_post', methods: ['POST'])]
    public function humanPost(  Request $request,
        QueryBus $queryBus,
        HubInterface $hub,
        string $id,
        string $participant
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $study = $queryBus->ask(FindStudyByIdQuery::fromId(Uuid::fromRaw($id)))->getStudy();

        if ($study === null) {
            throw $this->createNotFoundException();
        }

        $p = $queryBus->ask(FindParticipantByIdQuery::create($participant))->getParticipant();

        if ($p === null) {
            throw $this->createNotFoundException();
        }

        $body = $request->toArray();
        // MARK DDD this?
        $update = new Update(
            'http://kunlabo.com/engines/' . $id . '/' . $participant,
            json_encode($body)
        );
        $hub->publish($update);

        return new Response();
    }

}