<?php

namespace Kunlabo\User\Application\Command\SignUp;

use Kunlabo\Shared\Application\Bus\Command\Command;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\User\Domain\ValueObject\Email;
use Kunlabo\User\Domain\ValueObject\HashedPassword;

final class SignUpCommand implements Command
{
    private function __construct(private Uuid $uuid, private Email $email, private HashedPassword $hashedPassword)
    {
    }

    public static function fromRaw(string $uuid, string $email, string $plainPassword): self
    {
        return new self(
            Uuid::fromRaw($uuid),
            Email::fromRaw($email),
            HashedPassword::fromPlain($plainPassword)
        );
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
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
