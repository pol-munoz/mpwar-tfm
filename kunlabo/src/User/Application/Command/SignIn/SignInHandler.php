<?php

namespace Kunlabo\User\Application\Command\SignIn;

use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;
use Kunlabo\User\Domain\Exception\InvalidCredentialsException;
use Kunlabo\User\Domain\UserRepository;

final class SignInHandler implements CommandHandler
{
    public function __construct(private DomainEventBus $eventBus, private UserRepository $repository)
    {
    }

    public function __invoke(SignInCommand $command): void
    {
        $user = $this->repository->readByEmail($command->getEmail());

        if ($user === null) {
            throw new InvalidCredentialsException();
        }

        $user->signIn($command->getPlainPassword());

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
