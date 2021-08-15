<?php

namespace Kunlabo\User\Infrastructure\Framework\Auth;

use Kunlabo\User\Domain\User;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class AuthUser extends User implements UserInterface, PasswordHasherAwareInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_PREFIX = 'ROLE_';

    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_RESEARCHER = 'ROLE_RESEARCHER';

    public static function fromDomainUser(User $user): self
    {
        return new self(
            $user->getId(),
            $user->getCreated(),
            $user->getModified(),
            $user->getName(),
            $user->getEmail(),
            $user->getHashedPassword(),
            $user->getRoles()
        );
    }

    public function getRoles(): array
    {
        return array_map(function ($role) {
            return self::ROLE_PREFIX . strtoupper($role->getRaw());
        }, parent::getRoles());
    }

    public function getPassword(): string
    {
        return $this->getHashedPassword()->getHash();
    }

    public function getSalt(): ?string
    {
        // Returning a salt is only needed, if you are not using a modern hashing algorithm (e.g. bcrypt or sodium)
        return null;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getUsername(): string
    {
        return $this->getEmail()->getRaw();
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail()->getRaw();
    }

    public function getPasswordHasherName(): ?string
    {
        // See security.yaml
        return 'hasher';
    }
}