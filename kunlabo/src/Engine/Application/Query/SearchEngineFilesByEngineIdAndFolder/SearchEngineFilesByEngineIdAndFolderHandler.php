<?php

namespace Kunlabo\Engine\Application\Query\SearchEngineFilesByEngineIdAndFolder;

use Kunlabo\Engine\Application\EngineFilesResponse;
use Kunlabo\Engine\Domain\EngineRepository;
use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class SearchEngineFilesByEngineIdAndFolderHandler implements QueryHandler
{
    public function __construct(private EngineRepository $repository) {
    }

    public function __invoke(SearchEngineFilesByEngineIdAndFolderQuery $query): Response
    {
        $files = $this->repository->readFilesForEngineIdAndFolder($query->getEngineId(), $query->getFolder());

        return new EngineFilesResponse($files);
    }
}