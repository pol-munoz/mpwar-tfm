<?php

namespace Kunlabo\Agent\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Kunlabo\Agent\Domain\Agent;
use Kunlabo\Agent\Domain\AgentFile;
use Kunlabo\Agent\Domain\AgentRepository;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class DoctrineAgentRepository implements AgentRepository
{
    private ObjectRepository $repository;
    private ObjectRepository $fileRepository;

    public function __construct(private EntityManagerInterface $manager)
    {
        $this->repository = $manager->getRepository(Agent::class);
        $this->fileRepository = $manager->getRepository(AgentFile::class);
    }

    public function create(Agent $agent): void
    {
        $this->manager->persist($agent);
        $this->manager->flush();
    }

    public function createFile(AgentFile $file): void
    {
        $this->manager->persist($file);
        $this->manager->flush();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function readById(Uuid $id): ?Agent
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

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function readFileByAgentIdAndPath(Uuid $agent, string $path): ?AgentFile
    {
        return $this->fileRepository->findOneBy(
            ['agent' => $agent, 'path' => $path]
        );
    }

    public function readFilesForAgentId(Uuid $agent): array
    {
        return $this->fileRepository->findBy(
            ['agent' => $agent]
        );
    }

    public function update(Agent $agent): void
    {
        $this->manager->flush();
    }

    public function updateFile($file): void
    {
        $this->manager->flush();
    }
}