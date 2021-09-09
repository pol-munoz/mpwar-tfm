<?php

namespace Kunlabo\Participant\Application\Query\SearchNewParticipantsByStudyId;

use Kunlabo\Participant\Application\Query\ParticipantsResponse;
use Kunlabo\Participant\Domain\ParticipantRepository;
use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class SearchNewParticipantByStudyIdHandler implements QueryHandler
{
    public function __construct(private ParticipantRepository $repository)
    {
    }

    public function __invoke(SearchNewParticipantsByStudyIdQuery $query): Response
    {
        $participants = $this->repository->readNewForStudy($query->getStudyId());

        return new ParticipantsResponse($participants);
    }
}