<?php

namespace Kunlabo\Engine\Application\Query\SearchEngineFilesByEngineIdAndFolder;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class SearchEngineFilesByEngineIdAndFolderQuery implements Query
{
    private function __construct(private Uuid $engineId, private string $folder)
    {
    }

    public static function create(string $engineId, string $folder): self
    {
        return new self(
            Uuid::fromRaw($engineId),
            $folder
        );
    }

    public function getEngineId(): Uuid
    {
        return $this->engineId;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }
}