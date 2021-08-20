<?php

namespace Kunlabo\User\Infrastructure\Framework\Auth;

use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\User\Application\Query\SearchUserByEmail\SearchUserByEmailQuery;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class AuthUserProvider implements UserProviderInterface
{
    public function __construct(private QueryBus $queryBus)
    {
    }

    public function supportsClass(string $class)
    {
        return AuthUser::class === $class;
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function loadUserByUsername(string $username)
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->queryBus->ask(SearchUserByEmailQuery::create($identifier))->getUser();

        if ($user === null) {
            throw new UserNotFoundException();
        }

        return AuthUser::fromDomainUser($user);
    }
}