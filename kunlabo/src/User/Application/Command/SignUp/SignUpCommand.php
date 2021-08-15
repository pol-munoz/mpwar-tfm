<?php

namespace Kunlabo\User\Application\Command\SignUp;

use Kunlabo\Shared\Application\Bus\Command\Command;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\Shared\Domain\ValueObject\Name;
use Kunlabo\User\Domain\ValueObject\Email;
use Kunlabo\User\Domain\ValueObject\HashedPassword;

final class SignUpCommand implements Command
{
    private function __construct(
        private Uuid $uuid,
        private Name $name,
        private Email $email,
        private HashedPassword $hashedPassword
    ) {
    }

    public static function fromRaw(
        string $uuid,
        string $name,
        string $email,
        string $plainPassword
    ): self {
        return new self(
            Uuid::fromRaw($uuid),
            Name::fromRaw($name),
            Email::fromRaw($email),
            HashedPassword::fromPlain($plainPassword)
        );
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getHashedPassword(): HashedPassword
    {
        return $this->hashedPassword;
    }
}
