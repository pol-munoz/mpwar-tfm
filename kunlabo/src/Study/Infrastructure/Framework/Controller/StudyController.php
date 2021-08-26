<?php

namespace Kunlabo\Study\Infrastructure\Framework\Controller;

use DomainException;
use Kunlabo\Agent\Application\Query\FindAgentById\FindAgentByIdQuery;
use Kunlabo\Participant\Application\Command\DeleteParticipant\DeleteParticipantCommand;
use Kunlabo\Participant\Application\Query\SearchParticipantsByStudyId\SearchParticipantsByStudyIdQuery;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Study\Application\Query\FindStudyById\FindStudyByIdQuery;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class StudyController extends AbstractController
{
    #[Route('/{id}', name: 'web_studies_by_id', methods: ['GET'])]
    public function engine(
        QueryBus $queryBus,
        string $id
    ): Response {
        try {
            $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

            $study = $queryBus->ask(FindStudyByIdQuery::create($id))->getStudy();

            if ($study === null) {
                throw $this->createNotFoundException();
            }

            $agent = $queryBus->ask(FindAgentByIdQuery::create($study->getAgentId()))->getAgent();
            $human = $agent->getKind()->isHuman();

            $participants = $queryBus->ask(SearchParticipantsByStudyIdQuery::create($id))->getParticipants();

            return $this->render(
                'app/studies/study.html.twig',
                ['study' => $study, 'participants' => $participants, 'human' => $human]
            );
        } catch (DomainException) {
            throw $this->createNotFoundException();
        }
    }

    #[Route('/{id}/{participant}/delete', name: 'web_studies_participant_delete', methods: ['GET'])]
    public function participantDelete(
        CommandBus $commandBus,
        UrlGeneratorInterface $urlGenerator,
        string $id,
        string $participant
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $commandBus->dispatch(DeleteParticipantCommand::create($participant, $id));

        return new RedirectResponse($urlGenerator->generate('web_studies_by_id', ['id' => $id]), Response::HTTP_SEE_OTHER);
    }
}