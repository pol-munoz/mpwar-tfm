<?php

namespace Kunlabo\User\Application\Command\SignUp;

use Kunlabo\Shared\Application\Bus\Command\CommandHandler;
use Kunlabo\Shared\Application\Bus\Event\DomainEventBus;
use Kunlabo\User\Domain\Exception\UserAlreadyExistsException;
use Kunlabo\User\Domain\User;
use Kunlabo\User\Domain\UserRepository;
use Kunlabo\User\Domain\ValueObject\Role;

final class SignUpHandler implements CommandHandler
{
    public function __construct(private DomainEventBus $eventBus, private UserRepository $repository) {
    }

    public function __invoke(SignUpCommand $command): void
    {
        if ($this->repository->readByEmail($command->getEmail()) !== null) {
            throw new UserAlreadyExistsException();
        }

        $user = User::signUp($command->getUuid(), $command->getEmail(), $command->getHashedPassword());
        $user->addRole(Role::createUserRole());

        $this->repository->create($user);

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
