<?php

namespace Kunlabo\User\Infrastructure\Framework\Auth;

use Kunlabo\User\Domain\UserRepository;
use Kunlabo\User\Domain\ValueObject\Email;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class AuthUserProvider implements UserProviderInterface
{
    public function __construct(private UserRepository $repository)
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
        $user = $this->repository->readByEmail(Email::fromRaw($identifier));

        if ($user === null) {
            throw new UserNotFoundException();
        }

        return AuthUser::fromDomainUser($user);
    }
}