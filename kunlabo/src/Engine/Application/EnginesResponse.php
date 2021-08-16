<?php

namespace Kunlabo\Engine\Application;

use Kunlabo\Shared\Application\Bus\Query\Response;

final class EnginesResponse implements Response
{
    public function __construct(private array $engines)
    {
    }

    public function getEngines(): array
    {
        return $this->engines;
    }
}