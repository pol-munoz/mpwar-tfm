<?php

namespace Kunlabo\Study\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\Study\Domain\Study;
use Kunlabo\Study\Domain\StudyRepository;

final class DoctrineStudyRepository implements StudyRepository
{
    private ObjectRepository $repository;

    public function __construct(private EntityManagerInterface $manager)
    {
        $this->repository = $manager->getRepository(Study::class);
    }

    public function create(Study $study): void
    {
        $this->manager->persist($study);
        $this->manager->flush();
    }

    public function readById(Uuid $id): ?Study
    {
        return $this->repository->find($id);
    }

    public function readAllForUser(Uuid $owner): array
    {
        return $this->repository->findBy(
            ['owner' => $owner],
            ['modified' => 'DESC']
        );
    }

    public function readAllByAgentId(Uuid $agentId): array
    {
        return $this->repository->findBy(
            ['agentId' => $agentId]
        );
    }

    public function readAllByEngineId(Uuid $engineId): array
    {
        return $this->repository->findBy(
            ['engineId' => $engineId]
        );
    }

    public function delete(Study $study): void
    {
        $this->manager->remove($study);
        $this->manager->flush();
    }
}