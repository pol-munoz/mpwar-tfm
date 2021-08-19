<?php

namespace Kunlabo\Study\Application\Query\SearchStudiesByOwnerId;

use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;
use Kunlabo\Study\Application\Query\StudiesResponse;
use Kunlabo\Study\Domain\StudyRepository;

final class SearchStudiesByOwnerIdHandler implements QueryHandler
{
    public function __construct(private StudyRepository $repository) {
    }

    public function __invoke(SearchStudiesByOwnerIdQuery $query): Response
    {
        $studies = $this->repository->readAllForUser($query->getOwnerId());

        return new StudiesResponse($studies);
    }
}