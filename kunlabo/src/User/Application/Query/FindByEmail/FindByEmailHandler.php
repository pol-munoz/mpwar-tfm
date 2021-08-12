<?php

namespace Kunlabo\User\Application\Query\FindByEmail;

use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;
use Kunlabo\User\Application\Query\UserResponse;
use Kunlabo\User\Domain\UserRepository;

final class FindByEmailHandler implements QueryHandler
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function __invoke(FindByEmailQuery $query): Response
    {
        $user = $this->repository->readByEmail($query->getEmail());

        return new UserResponse($user);
    }
}
