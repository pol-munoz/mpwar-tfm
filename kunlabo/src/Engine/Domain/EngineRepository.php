<?php

namespace Kunlabo\Engine\Domain;

use Kunlabo\Shared\Domain\ValueObject\Uuid;

interface EngineRepository
{
    public function create(Engine $engine): void;
    public function createFile(EngineFile $file): void;

    public function readById(Uuid $id): ?Engine;
    public function readAllForUser(Uuid $owner): array;

    public function update(Engine $engine): void;
}