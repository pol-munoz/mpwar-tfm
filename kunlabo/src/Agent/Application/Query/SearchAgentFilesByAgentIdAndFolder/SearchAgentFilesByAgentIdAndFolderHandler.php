<?php

namespace Kunlabo\Agent\Application\Query\SearchAgentFilesByAgentIdAndFolder;

use Kunlabo\Agent\Application\AgentFilesResponse;
use Kunlabo\Agent\Domain\AgentRepository;
use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class SearchAgentFilesByAgentIdAndFolderHandler implements QueryHandler
{
    public function __construct(private AgentRepository $repository) {
    }

    public function __invoke(SearchAgentFilesByAgentIdAndFolderQuery $query): Response
    {
        $files = $this->repository->readFilesForAgentIdAndFolder($query->getAgentId(), $query->getFolder());

        return new AgentFilesResponse($files);
    }
}