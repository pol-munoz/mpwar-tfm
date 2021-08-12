<?php

namespace Kunlabo\User\Domain;

use Kunlabo\Shared\Domain\Aggregate\AggregateRoot;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\User\Domain\Event\UserSignedInEvent;
use Kunlabo\User\Domain\Event\UserSignedUpEvent;
use Kunlabo\User\Domain\Exception\InvalidCredentialsException;
use Kunlabo\User\Domain\ValueObject\Email;
use Kunlabo\User\Domain\ValueObject\HashedPassword;
use Kunlabo\User\Domain\ValueObject\Role;

class User extends AggregateRoot
{
    protected function __construct(
        Uuid $id,
        private Email $email,
        private HashedPassword $hashedPassword,
        protected array $roles = []
    ) {
        parent::__construct($id);
    }

    public static function signUp(
        Uuid $id,
        Email $email,
        HashedPassword $hashedPassword
    ): self {
        $user = new self($id, $email, $hashedPassword);
        $user->record(new UserSignedUpEvent($user));

        return $user;
    }

    public function signIn(string $plainPassword)
    {
        if (!$this->hashedPassword->match($plainPassword)) {
            throw new InvalidCredentialsException();
        }
        $this->record(new UserSignedInEvent($this));
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getHashedPassword(): HashedPassword
    {
        return $this->hashedPassword;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function addRole(Role $role): void
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }
}