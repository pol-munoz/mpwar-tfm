<?php

namespace Kunlabo\Action\Infrastructure;

use Kunlabo\Action\Domain\Action;
use Kunlabo\Action\Domain\LogService;

final class ElkLogService implements LogService
{
    public function logAction(Action $action): void
    {
        // TODO: Implement logAction() method.
    }
}