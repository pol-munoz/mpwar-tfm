<?php

namespace Kunlabo\Agent\Domain;

use Kunlabo\Shared\Domain\ValueObject\Uuid;

interface AgentRepository
{
    public function create(Agent $agent): void;
    public function createFile(AgentFile $file): void;

    public function readById(Uuid $id): ?Agent;
    public function readAllForUser(Uuid $owner): array;
    public function readFileByAgentIdAndPath(Uuid $agent, string $path): ?AgentFile;
    public function readFilesForAgentId(Uuid $agent): array;

    public function update(Agent $agent): void;
    public function updateFile($file): void;

    public function delete(Agent $agent): void;
    public function deleteFile(AgentFile $file): void;
}