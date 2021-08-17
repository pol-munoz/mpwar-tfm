<?php

namespace Kunlabo\Engine\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Kunlabo\Engine\Domain\Engine;
use Kunlabo\Engine\Domain\EngineFile;
use Kunlabo\Engine\Domain\EngineRepository;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class DoctrineEngineRepository implements EngineRepository
{
    private ObjectRepository $repository;

    public function __construct(private EntityManagerInterface $manager)
    {
        $this->repository = $manager->getRepository(Engine::class);
        $this->fileRepository = $manager->getRepository(EngineFile::class);
    }

    public function create(Engine $engine): void
    {
        $this->manager->persist($engine);
        $this->manager->flush();
    }

    public function createFile(EngineFile $file): void
    {
        $this->manager->persist($file);
        $this->manager->flush();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function readById(Uuid $id): ?Engine
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

    public function update(Engine $engine): void
    {
        $this->manager->flush();
    }
}