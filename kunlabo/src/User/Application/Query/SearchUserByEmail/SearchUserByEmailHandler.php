<?php

namespace Kunlabo\User\Application\Query\SearchUserByEmail;

use Kunlabo\Shared\Application\Bus\Query\QueryHandler;
use Kunlabo\Shared\Application\Bus\Query\Response;
use Kunlabo\User\Application\Query\UserResponse;
use Kunlabo\User\Domain\UserRepository;

final class SearchUserByEmailHandler implements QueryHandler
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function __invoke(SearchUserByEmailQuery $query): Response
    {
        $user = $this->repository->readByEmail($query->getEmail());

        return new UserResponse($user);
    }
}
