<?php

namespace Kunlabo\Log\Application\Query;

use Kunlabo\Shared\Application\Bus\Query\Response;

final class LogsResponse implements Response
{
    public function __construct(private array $logs)
    {
    }

    public function getLogs(): array
    {
        return $this->logs;
    }
}