<?php

namespace Kunlabo\Participant\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Kunlabo\Participant\Domain\Participant;
use Kunlabo\Participant\Domain\ParticipantRepository;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class DoctrineParticipantRepository implements ParticipantRepository
{
    private ObjectRepository $repository;

    public function __construct(private EntityManagerInterface $manager)
    {
        $this->repository = $manager->getRepository(Participant::class);
    }

    public function create(Participant $participant): void
    {
        $this->manager->persist($participant);
        $this->manager->flush();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function readById(Uuid $id): ?Participant
    {
        return $this->repository->find($id);
    }

    public function readAllForStudy(Uuid $study): array
    {
        return $this->repository->findBy(
            ['studyId' => $study],
            ['modified' => 'DESC']
        );
    }

    public function delete(Participant $participant): void
    {
        $this->manager->remove($participant);
        $this->manager->flush();
    }
}