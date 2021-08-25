<?php

namespace Kunlabo\Log\Infrastructure\Framework\Controller;

use Kunlabo\Log\Application\Query\SearchLogsByStudyAndParticipant\SearchLogsByStudyAndParticipantQuery;
use Kunlabo\Participant\Application\Query\FindParticipantById\FindParticipantByIdQuery;
use Kunlabo\Participant\Domain\Participant;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Study\Application\Query\FindStudyById\FindStudyByIdQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class LogController extends AbstractController
{

    #[Route('/{id}/{participant}', name: 'web_logs_by_participant', methods: ['GET'])]
    public function participant(
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

        $results = $queryBus->ask(SearchLogsByStudyAndParticipantQuery::create($id, $participant))->getLogs();

        $response = new JsonResponse($this->participantLogsToArray($p, $results));
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