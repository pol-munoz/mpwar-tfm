<?php

namespace Kunlabo\Agent\Application;

use Kunlabo\Shared\Application\Bus\Query\Response;

final class AgentFilesResponse implements Response
{
    public function __construct(private array $files)
    {
    }

    public function getAgentFiles(): array
    {
        return $this->files;
    }
}