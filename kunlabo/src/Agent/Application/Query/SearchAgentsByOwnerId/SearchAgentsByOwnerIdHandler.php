<?php

namespace Kunlabo\Agent\Application\Query\SearchAgentsByOwnerId;

use Kunlabo\Agent\Application\AgentsResponse;
use Kunlabo\Agent\Domain\AgentRepository;
use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class SearchAgentsByOwnerIdHandler implements QueryHandler
{
    public function __construct(private AgentRepository $repository) {
    }

    public function __invoke(SearchAgentsByOwnerIdQuery $query): Response
    {
        $agents = $this->repository->readAllForUser($query->getOwnerId());

        return new AgentsResponse($agents);
    }

}