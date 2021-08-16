<?php

namespace Kunlabo\Engine\Application\Query\FindEngineById;

use Kunlabo\Engine\Application\EngineResponse;
use Kunlabo\Engine\Domain\EngineRepository;
use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class FindEngineByIdHandler implements QueryHandler
{
    public function __construct(private EngineRepository $repository)
    {
    }

    public function __invoke(FindEngineByIdQuery $query): Response
    {
        $engine = $this->repository->readById($query->getId());

        return new EngineResponse($engine);
    }
}