<?php

namespace Kunlabo\Engine\Application\Query\SearchEngineFilesByEngineId;

use Kunlabo\Engine\Application\EngineFilesResponse;
use Kunlabo\Engine\Domain\EngineRepository;
use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class SearchEngineFilesByEngineIdHandler implements QueryHandler
{
    public function __construct(private EngineRepository $repository) {
    }

    public function __invoke(SearchEngineFilesByEngineIdQuery $query): Response
    {
        $files = $this->repository->readFilesForEngineId($query->getEngineId());

        return new EngineFilesResponse($files);
    }
}