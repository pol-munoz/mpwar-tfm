<?php

namespace Kunlabo\Log\Infrastructure\Framework\Controller;

use Kunlabo\Log\Application\Query\SearchLogsByStudyAndParticipant\SearchLogsByStudyAndParticipantQuery;
use Kunlabo\Participant\Application\Query\FindParticipantById\FindParticipantByIdQuery;
use Kunlabo\Participant\Application\Query\SearchParticipantsByStudyId\SearchParticipantsByStudyIdQuery;
use Kunlabo\Participant\Domain\Participant;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Study\Application\Query\FindStudyById\FindStudyByIdQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

final class LogController extends AbstractController
{

    #[Route('/{id}/{participant}', name: 'web_logs_by_participant', methods: ['GET'])]
    public function participantLogs(
        QueryBus $queryBus,
        Security $security,
        string $id,
        string $participant
    ): Response {
        $study = $queryBus->ask(FindStudyByIdQuery::create($id))->getStudy();

        $owner = $security->getUser()->getId();
        if ($study === null || !$study->isOwnedBy($owner)) {
            throw $this->createNotFoundException();
        }

        $p = $queryBus->ask(FindParticipantByIdQuery::create($participant))->getParticipant();

        if ($p === null) {
            throw $this->createNotFoundException();
        }

        $results = $queryBus->ask(SearchLogsByStudyAndParticipantQuery::create($id, $participant))->getLogs();

        $response = new JsonResponse($this->participantLogsToArray($p, $results));
        $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );

        return $response;
    }

    #[Route('/{id}', name: 'web_logs_by_study', methods: ['GET'])]
    public function studyLogs(
        QueryBus $queryBus,
        Security $security,
        string $id
    ): Response {
        $study = $queryBus->ask(FindStudyByIdQuery::create($id))->getStudy();

        $owner = $security->getUser()->getId();
        if ($study === null || !$study->isOwnedBy($owner)) {
            throw $this->createNotFoundException();
        }

        $participants = $queryBus->ask(SearchParticipantsByStudyIdQuery::create($id))->getParticipants();

        $response = [];

        foreach ($participants as $participant) {
            $results = $queryBus->ask(SearchLogsByStudyAndParticipantQuery::create($id, $participant->getId()->getRaw()))->getLogs();

            $response[] = $this->participantLogsToArray($participant, $results);
        }

        $response = new JsonResponse($response);
        $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );

        return $response;
    }

    private function participantLogsToArray(Participant $participant, array $logs): array
    {
        return [
            'participant' => [
                'nickname' => $participant->getName()->getRaw(),
                'age' => $participant->getAge()->getRaw(),
                'gender' => $participant->getGender()->getRaw(),
                'handedness' => $participant->getHandedness()->getRaw(),
            ],
            'logs' => array_map(
                function ($log) {
                    return [
                        'timestamp' => $log->getTimestamp(),
                        'log' => $log->getBody(),
                    ];
                },
                $logs
            )
        ];
    }
}