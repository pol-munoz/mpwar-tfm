<?php

namespace Kunlabo\Agent\Application\Query\FindAgentById;

use Kunlabo\Agent\Application\AgentResponse;
use Kunlabo\Agent\Domain\AgentRepository;
use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class FindAgentByIdHandler implements QueryHandler
{
    public function __construct(private AgentRepository $repository)
    {
    }

    public function __invoke(FindAgentByIdQuery $query): Response
    {
        $agent = $this->repository->readById($query->getId());

        return new AgentResponse($agent);
    }
}