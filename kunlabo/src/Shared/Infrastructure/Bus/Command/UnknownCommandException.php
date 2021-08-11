<?php

namespace Kunlabo\Shared\Infrastructure\Bus\Command;

use Kunlabo\Shared\Application\Bus\Command\Command;
use RuntimeException;

final class UnknownCommandException extends RuntimeException
{
    public function __construct(Command $command)
    {
        $commandClass = get_class($command);

        parent::__construct("Unknown command (no handler): " . $commandClass);
    }
}