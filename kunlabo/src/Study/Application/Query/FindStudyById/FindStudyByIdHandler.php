<?php

namespace Kunlabo\Study\Application\Query\FindStudyById;

use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;
use Kunlabo\Study\Application\Query\StudyResponse;
use Kunlabo\Study\Domain\StudyRepository;

final class FindStudyByIdHandler implements QueryHandler
{
    public function __construct(private StudyRepository $repository)
    {
    }

    public function __invoke(FindStudyByIdQuery $query): Response
    {
        $study = $this->repository->readById($query->getId());

        return new StudyResponse($study);
    }

}