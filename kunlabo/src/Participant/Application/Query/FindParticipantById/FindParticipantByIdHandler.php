<?php

namespace Kunlabo\Participant\Application\Query\FindParticipantById;

use Kunlabo\Participant\Application\Query\ParticipantResponse;
use Kunlabo\Participant\Domain\ParticipantRepository;
use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class FindParticipantByIdHandler implements QueryHandler
{
    public function __construct(private ParticipantRepository $repository)
    {
    }

    public function __invoke(FindParticipantByIdQuery $query): Response
    {
        $participant = $this->repository->readById($query->getId());

        return new ParticipantResponse($participant);
    }
}