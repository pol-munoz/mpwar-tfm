<?php

namespace Kunlabo\Log\Application\Query\SearchLogsByStudyAndParticipant;

use Kunlabo\Log\Application\Query\LogsResponse;
use Kunlabo\Log\Domain\LogRepository;
use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class SearchLogsByStudyAndParticipantHandler implements QueryHandler
{
    public function __construct(private LogRepository $repository) {
    }

    public function __invoke(SearchLogsByStudyAndParticipantQuery $query): Response
    {
        $logs = $this->repository->readAllByStudyAndParticipantId($query->getStudyId(), $query->getParticipantId());

        return new LogsResponse($logs);
    }
}