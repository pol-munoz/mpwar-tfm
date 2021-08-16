<?php

namespace Kunlabo\User\Application\Command\SignIn;

use Kunlabo\Shared\Application\Bus\Command\Command;
use Kunlabo\User\Domain\ValueObject\Email;

final class SignInCommand implements Command
{
    private function __construct(private Email $email, private string $plainPassword)
    {
    }

    public static function create(string $email, string $plainPassword): self
    {
        return new self(
            Email::fromRaw($email),
            $plainPassword
        );
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }
}
