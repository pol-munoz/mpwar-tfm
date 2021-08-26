<?php

namespace Kunlabo\Study\Domain;

use Kunlabo\Shared\Domain\ValueObject\Uuid;

interface StudyRepository
{
    public function create(Study $study): void;

    public function readById(Uuid $id): ?Study;
    public function readAllForUser(Uuid $owner): array;
    public function readAllByAgentId(Uuid $agentId): array;
    public function readAllByEngineId(Uuid $engineId): array;

    public function delete(Study $study): void;
}