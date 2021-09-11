<?php

namespace Kunlabo\Log\Application\Query\SearchNewLogsByStudy;

use Kunlabo\Log\Application\Query\LogsResponse;
use Kunlabo\Log\Domain\LogRepository;
use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class SearchNewLogsByStudyHandler implements QueryHandler
{
    public function __construct(private LogRepository $repository) {
    }

    public function __invoke(SearchNewLogsByStudyQuery $query): Response
    {
        $logs = $this->repository->readNewByStudyId($query->getStudyId());

        return new LogsResponse($logs);
    }
}