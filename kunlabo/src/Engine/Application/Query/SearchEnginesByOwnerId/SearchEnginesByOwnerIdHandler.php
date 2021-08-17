<?php

namespace Kunlabo\Engine\Application\Query\SearchEnginesByOwnerId;

use Kunlabo\Engine\Application\EnginesResponse;
use Kunlabo\Engine\Domain\EngineRepository;
use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class SearchEnginesByOwnerIdHandler implements QueryHandler
{
    public function __construct(private EngineRepository $repository) {
    }

    public function __invoke(SearchEnginesByOwnerIdQuery $query): Response
    {
        $engines = $this->repository->readAllForUser($query->getOwnerId());

        return new EnginesResponse($engines);
    }

}