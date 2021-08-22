<?php

namespace Kunlabo\Participant\Application\Query\SearchParticipantsByStudyId;

use Kunlabo\Participant\Application\Query\ParticipantsResponse;
use Kunlabo\Participant\Domain\ParticipantRepository;
use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class SearchParticipantsByStudyIdHandler implements QueryHandler
{
    public function __construct(private ParticipantRepository $repository)
    {
    }

    public function __invoke(SearchParticipantsByStudyIdQuery $query): Response
    {
        $participants = $this->repository->readAllForStudy($query->getStudyId());

        return new ParticipantsResponse($participants);
    }
}