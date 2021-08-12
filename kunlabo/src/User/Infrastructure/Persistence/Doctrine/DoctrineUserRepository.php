<?php

namespace Kunlabo\User\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\User\Domain\User;
use Kunlabo\User\Domain\UserRepository;
use Kunlabo\User\Domain\ValueObject\Email;

final class DoctrineUserRepository implements UserRepository
{
    private ObjectRepository $repository;

    public function __construct(private EntityManagerInterface $manager)
    {
        $this->repository = $manager->getRepository(User::class);
    }

    public function create(User $user)
    {
        $this->manager->persist($user);
        $this->manager->flush();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function readById(Uuid $id): User
    {
        return $this->repository->find($id);
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function readByEmail(Email $email): ?User
    {
        return $this->repository->findOneBy(
            ['email.raw' => $email->getRaw()],
        );
    }
}