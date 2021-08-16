<?php

namespace Kunlabo\Engine\Application;

use Kunlabo\Engine\Domain\Engine;
use Kunlabo\Shared\Application\Bus\Query\Response;

final class EngineResponse implements Response
{
    public function __construct(private Engine $engine)
    {
    }

    public function getEngine(): Engine
    {
        return $this->engine;
    }
}