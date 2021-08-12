<?php

namespace Kunlabo\User\Application\Query\FindByIdQuery;

use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;
use Kunlabo\User\Application\Query\UserResponse;
use Kunlabo\User\Domain\UserRepository;

final class FindByIdHandler implements QueryHandler
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function __invoke(FindByIdQuery $query): Response
    {
        $user = $this->repository->readById($query->getId());

        return new UserResponse($user);
    }
}