<?php

namespace Kunlabo\Engine\Application;

use Kunlabo\Shared\Application\Bus\Query\Response;

final class EngineFilesResponse implements Response
{
    public function __construct(private array $files)
    {
    }

    public function getEngineFiles(): array
    {
        return $this->files;
    }
}