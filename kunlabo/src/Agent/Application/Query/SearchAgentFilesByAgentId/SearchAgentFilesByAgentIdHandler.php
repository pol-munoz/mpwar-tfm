<?php

namespace Kunlabo\Agent\Application\Query\SearchAgentFilesByAgentId;

use Kunlabo\Agent\Application\AgentFilesResponse;
use Kunlabo\Agent\Domain\AgentRepository;
use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class SearchAgentFilesByAgentIdHandler implements QueryHandler
{
    public function __construct(private AgentRepository $repository) {
    }

    public function __invoke(SearchAgentFilesByAgentIdQuery $query): Response
    {
        $files = $this->repository->readFilesForAgentId($query->getAgentId());

        return new AgentFilesResponse($files);
    }
}